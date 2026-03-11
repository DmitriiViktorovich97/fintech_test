FROM nginx:alpine

# Удаляем оригинальный default.conf чтобы entrypoint не ругался
RUN rm -f /etc/nginx/conf.d/default.conf

# Копируем свой конфиг
COPY nginx.conf /etc/nginx/conf.d/default.conf
