#!/bin/sh
go install --buildvcs=false && go build bot.go && go run bot;