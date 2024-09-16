package main

import (
	"fmt"
	"log"
	"os"

	"bot/telegramBot"

	"github.com/joho/godotenv"
)

func main() {
	err := godotenv.Load("/app/.env")
	if err != nil {
		log.Fatal("Error loading .env file")
	}
	
	fmt.Printf("Token: %s\n", os.Getenv("TELEGRAM_BOT_TOKEN"))

	telegramBot.SetToken(os.Getenv("TELEGRAM_BOT_TOKEN"))
	telegramBot.InitBot()
}
