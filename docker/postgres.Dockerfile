# syntax=docker/dockerfile:1.15.0

FROM postgres:17-alpine3.22@sha256:d5f196a551b5cef1c70853c6dd588f456d16ca4ea733e3f31c75bc1ae2f65f3f

ENV TZ=America/Sao_Paulo

RUN set -eux;\
    apk update;\
    apk add --no-cache tzdata nano ca-certificates;\
    apk add --no-cache postgresql-contrib postgis;\
    ln -snf /usr/share/zoneinfo/"${TZ}" /etc/localtime;\
    echo "${TZ}" > /etc/timezone;\
    update-ca-certificates;\
    rm -rf /var/cache/apk/*;
