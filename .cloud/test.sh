#!/bin/bash

set -e

docker compose -f .cloud/test/docker-compose.yaml build
docker compose -f .cloud/test/docker-compose.yaml run php
