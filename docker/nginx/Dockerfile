FROM nginx:alpine

RUN mkdir -p /app/public && \
    echo "<?php die('done!');" > /app/public/index.php

EXPOSE 80
