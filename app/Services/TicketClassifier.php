<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TicketCategory;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        if (config('services.openai.classify_enabled') === false) {
            return $this->getDummyClassification();
        }

        [$subject, $body] = $this->preprocess($subject, $body);

        try {
            $messages = array_merge(
                [
                    ['role' => 'system', 'content' => $this->getSystemPrompt()],
                ],
                $this->getFewShotMessages(),
                [
                    ['role' => 'user', 'content' => "Subject: {$subject}\n\nBody: {$body}"],
                ]
            );

            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model'),
                'messages' => $messages,
                'temperature' => 0.0,
                'presence_penalty' => 0.0,
                'frequency_penalty' => 0.0,
                'seed' => 42,
                'max_tokens' => 300,
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => $this->getJsonSchema(),
                ],
            ]);

            $json = $response->choices[0]?->message->content ?? '{}';
            $result = json_decode($json, true) ?? [];

            $result = $this->sanitizeResult($result);

            return [
                'category' => $result['category'],
                'explanation' => $result['explanation'],
                'confidence' => $result['confidence'],
            ];
        } catch (Exception $e) {
            Log::error('TicketClassifier OpenAI API call failed: ' . $e->getMessage() . $e->getTraceAsString());
            return $this->getDummyClassification();
        }
    }

    private function getSystemPrompt(): string
    {
        $categories = implode(', ', TicketCategory::values());
        return implode("\n", [
            "You are a meticulous support ticket classification expert.",
            "Task:",
            "- Classify the ticket into exactly one of these categories: {$categories}.",
            "- If multiple seem plausible, choose the best fit using the primary user intent.",
            "- The explanation must be brief (<= 2 sentences) and reference concrete cues from the text.",
            "- Output must be valid JSON strictly matching the provided schema.",
            "- confidence is your calibrated probability for the chosen category between 0 and 1.",
        ]);
    }

    /**
     * Few-shot examples, keep them short and close to real tickets
     */
    private function getFewShotMessages(): array
    {
        return [
            [
                'role' => 'user',
                'content' => "Subject: Can't log in to my account\n\nBody: I reset my password but still get an 'invalid credentials' error on the login page."
            ],
            [
                'role' => 'assistant',
                'content' => json_encode([
                    'category' => TicketCategory::TECHNICAL_SUPPORT->value,
                    'explanation' => "User cannot authenticate after password reset; core issue is access/login failure.",
                    'confidence' => 0.86,
                ], JSON_UNESCAPED_SLASHES),
            ],
            [
                'role' => 'user',
                'content' => "Subject: Billing address update\n\nBody: Please change my company billing address on the next invoice."
            ],
            [
                'role' => 'assistant',
                'content' => json_encode([
                    'category' => TicketCategory::BILLING->value,
                    'explanation' => "Request concerns invoice/billing information change.",
                    'confidence' => 0.88,
                ], JSON_UNESCAPED_SLASHES),
            ],
            [
                'role' => 'user',
                'content' => "Subject: Feature request: dark mode\n\nBody: Would love a dark theme option for the dashboard."
            ],
            [
                'role' => 'assistant',
                'content' => json_encode([
                    'category' => TicketCategory::GENERAL_INQUIRY->value,
                    'explanation' => "User proposes a new feature (dark mode) rather than a defect or incident.",
                    'confidence' => 0.84,
                ], JSON_UNESCAPED_SLASHES),
            ],
        ];
    }

    private function getJsonSchema(): array
    {
        $categories = TicketCategory::values();

        return [
            'name' => 'ticket_classification',
            'schema' => [
                'type' => 'object',
                'additionalProperties' => false,
                'required' => ['category', 'explanation', 'confidence'],
                'properties' => [
                    'category' => [
                        'type' => 'string',
                        'enum' => $categories,
                        'description' => 'One of the predefined ticket categories.',
                    ],
                    'explanation' => [
                        'type' => 'string',
                        'maxLength' => 280,
                        'description' => 'A concise rationale (<= 2 sentences) citing cues from the ticket.',
                    ],
                    'confidence' => [
                        'type' => 'number',
                        'minimum' => 0.0,
                        'maximum' => 1.0,
                        'description' => 'Estimated probability the category is correct.',
                    ],
                ],
            ],
        ];
    }

    private function sanitizeResult(array $result): array
    {
        $validCategories = TicketCategory::values();

        $category = $result['category'] ?? null;
        if (!is_string($category) || !in_array($category, $validCategories, true)) {
            $category = TicketCategory::GENERAL_INQUIRY->value;
        }

        $explanation = $result['explanation'] ?? 'AI could not determine a reason.';
        if (!is_string($explanation) || $explanation === '') {
            $explanation = 'AI could not determine a reason.';
        } else {
            $explanation = mb_substr($explanation, 0, 280);
        }

        $confidence = $result['confidence'] ?? 0.0;
        if (!is_numeric($confidence)) {
            $confidence = 0.0;
        }
        $confidence = max(0.0, min(1.0, (float) $confidence));

        return [
            'category' => $category,
            'explanation' => $explanation,
            'confidence' => $confidence,
        ];
    }

    private function preprocess(string $subject, string $body): array
    {
        $subject = trim(preg_replace('/\s+/', ' ', $subject));
        $body = trim($body);

        // We could have configs for these limits, but for now we'll keep it simple
        $subject = mb_substr($subject, 0, 200);
        $body = mb_substr($body, 0, 4000);

        return [$subject, $body];
    }

    private function getDummyClassification(): array
    {
        return [
            'category' => TicketCategory::cases()[array_rand(TicketCategory::cases())]->value,
            'explanation' => 'This is a dummy explanation because AI classification is disabled or failed.',
            'confidence' => round(mt_rand(10, 90) / 100, 2),
        ];
    }
}
