#!/bin/bash

# color codes
GREEN=$'\e[1;32m'
RED=$'\e[1;31m'
NC=$'\e[0m'

# env branch
branch=$(git branch | sed -n -e 's/^\* \(.*\)/\1/p');

# docker compose bin
if [ -x "$(command -v docker compose)" ]; then
  # new version
  bin='docker compose';
elif [ -x "$(command -v docker-compose)" ]; then
  # old version
  bin='docker-compose';
else
  echo "${RED}Docker compose not installed${NC}";
  exit;
fi

# validate env
if [[ ($branch == "develop" && $1 == "dev") || ($branch == "staging" && $1 == "staging") || (($branch == "master" || $branch == "main") && $1 == "prod") ]]; then
  export env=$1
else
  echo "${RED}Invalid command $1${NC}";
  exit;
fi

# docker composer project
project="task-${env}";

# action
action=$2

# run command
if [ "$action" == "up" ]; then
  $bin -f docker-compose.yml -p $project up -d
elif [ "$action" == "build" ]; then
  $bin -f docker-compose.yml -p $project up --build -d
elif [ "$action" == "down" ]; then
  $bin -f docker-compose.yml -p $project down -v
elif [ "$action" == "ps" ]; then
  $bin -f docker-compose.yml -p $project ps
elif [ "$action" == "exec" ]; then
  $bin -f docker-compose.yml -p $project exec ${@:3}
else
  echo "${RED}Invalid command $1${NC}";
  exit;
fi