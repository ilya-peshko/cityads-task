FROM nginx:1.27

RUN ln -sf /usr/share/zoneinfo/Europe/Moscow  /etc/localtime \
    && echo "Europe/Moscow" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata
