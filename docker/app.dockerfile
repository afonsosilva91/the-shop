FROM node

COPY app/package.json /app/package.json

WORKDIR /app

RUN npm install --silent

COPY app/ /app

RUN npm install -g --silent \
    react-scripts@1.1.1 \
    serve \
    && npm run build

CMD ["serve", "-s", "build"]