FROM sail-8.2/app

RUN apt-get update \
    && apt-get install -y --no-install-recommends supervisor \
    && rm -rf /var/lib/apt/lists/*

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/laravel-horizon.conf /etc/supervisor/conf.d/laravel-horizon.conf

ENTRYPOINT ["supervisord","-c","/etc/supervisor/supervisord.conf"]
