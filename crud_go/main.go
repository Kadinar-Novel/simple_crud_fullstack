package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"

	"github.com/gorilla/handlers"
	"github.com/gorilla/mux"
	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
)

var db *gorm.DB
var err error

type Karyawan struct {
	ID           int    `form:"id" json:"id"`
	NikKaryawan  string `form:"nik_karyawan" json:"nik_karyawan"`
	Nama         string `form:"nama" json:"nama"`
	Alamat       string `form:"alamat" json:"alamat"`
	Phone        string `form:"phone" json:"phone"`
	Email        string `form:"email" json:"email"`
	JenisKelamin string `form:"jenis_kelamin" json:"jenis_kelamin"`
}

type Result struct {
	Code    int         `json:"code"`
	Data    interface{} `json:"data"`
	Message string      `json:"message"`
}

// Main
func main() {
	db, err = gorm.Open("mysql", "root:@tcp(127.0.0.1:3306)/crud_go_db?charset=utf8&parseTime=True")

	if err != nil {
		log.Println("Connection failed", err)
	} else {
		log.Println("Connection established")
	}

	db.AutoMigrate(&Karyawan{})

	handleRequests()
}

func handleRequests() {
	log.Println("Start the development server at http://127.0.0.1:9999")

	myRouter := mux.NewRouter().StrictSlash(true)

	myRouter.NotFoundHandler = http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusNotFound)

		res := Result{Code: 404, Message: "Method not found"}
		response, _ := json.Marshal(res)
		w.Write(response)
	})

	myRouter.MethodNotAllowedHandler = http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusMethodNotAllowed)

		res := Result{Code: 403, Message: "Method not allowed"}
		response, _ := json.Marshal(res)
		w.Write(response)
	})

	myRouter.HandleFunc("/", homePage)
	myRouter.HandleFunc("/api/karyawan", createKaryawan).Methods("POST")
	myRouter.HandleFunc("/api/karyawan", getKaryawans).Methods("GET")
	myRouter.HandleFunc("/api/karyawan/{id}", getKaryawan).Methods("GET")
	myRouter.HandleFunc("/api/karyawan/{id}", updateKaryawan).Methods("PUT")
	myRouter.HandleFunc("/api/karyawan/{id}", deleteKaryawan).Methods("DELETE")

	headersOk := handlers.AllowedHeaders([]string{"X-Requested-With"})
	originsOk := handlers.AllowedOrigins([]string{"*"})
	methodsOk := handlers.AllowedMethods([]string{"GET", "HEAD", "POST", "PUT", "OPTIONS", "DELETE"})

	log.Fatal(http.ListenAndServe(":9999", handlers.CORS(originsOk, headersOk, methodsOk)(myRouter)))
}

func homePage(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "Welcome!")
}

func createKaryawan(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Access-Control-Allow-Origin", "*")
	w.Header().Set("Access-Control-Allow-Headers", "Content-Type")
	payloads, _ := ioutil.ReadAll(r.Body)
	//fmt.Println(string(payloads))
	var karyawan Karyawan
	json.Unmarshal(payloads, &karyawan)

	db.Create(&karyawan)

	res := Result{Code: 200, Data: karyawan, Message: "Data karyawan berhasil dibuat"}
	result, err := json.Marshal(res)

	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	w.Write(result)
}

func getKaryawans(w http.ResponseWriter, r *http.Request) {
	fmt.Println("Endpoint hit: get karyawan")

	karyawan := []Karyawan{}
	db.Find(&karyawan)

	res := Result{Code: 200, Data: karyawan, Message: "Daftar karyawan"}
	results, err := json.Marshal(res)

	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	w.Write(results)
}

func getKaryawan(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	karyawanID := vars["id"]

	var karyawan Karyawan

	db.First(&karyawan, karyawanID)

	res := Result{Code: 200, Data: karyawan, Message: "Data karyawan berhasil ditemukan"}
	result, err := json.Marshal(res)

	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	w.Write(result)
}

func updateKaryawan(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	karyawanID := vars["id"]

	payloads, _ := ioutil.ReadAll(r.Body)

	var karyawanUpdates Karyawan
	json.Unmarshal(payloads, &karyawanUpdates)

	var karyawan Karyawan
	db.First(&karyawan, karyawanID)
	db.Model(&karyawan).Updates(karyawanUpdates)

	res := Result{Code: 200, Data: karyawan, Message: "Data karyawan berhasil diupdate"}
	result, err := json.Marshal(res)

	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	w.Write(result)
}

func deleteKaryawan(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	karyawanID := vars["id"]

	var karyawan Karyawan

	db.First(&karyawan, karyawanID)
	db.Delete(&karyawan)

	res := Result{Code: 200, Message: "Data karyawan berhasil dihapus"}
	result, err := json.Marshal(res)

	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	w.Write(result)
}
