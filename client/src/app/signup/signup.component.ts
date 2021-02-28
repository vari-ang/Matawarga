import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpParams } from '@angular/common/http';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.scss'],
})
export class SignupComponent implements OnInit {
  constructor(private http: HttpClient) { }

  name:string = '';
  username:string = '';
  password: string = '';
  rePassword: string = '';
  signup = {
    color: 'danger',
    isError: false,
    message: ''
  }

  ngOnInit() {}

  getName(event) {
    this.name = event.target.value;
  }

  getUsername(event) {
    this.username = event.target.value;
  }

  getPassword(event) {
    this.password = event.target.value;
  }

  getRePassword(event) {
    this.rePassword = event.target.value;
  }

  signUpHttp(name:string, username:string, password:string):Observable<any> {
    let body = new HttpParams();
    body = body.set('name', name);
    body = body.set('username', username);
    body = body.set('password', password);
  	// return this.http.post("http://ubaya.prototipe.net/s160717023/matawarga/signup.php", body);
    return this.http.post("http://localhost:8080/projects/web/matawarga/server/signup.php", body);
  }

  signUp() {
    var ng = this; 

    if(!ng.name || !ng.username || !ng.password || !ng.rePassword) {
      ng.signup.color = 'danger';
      ng.signup.isError = true;
      ng.signup.message = 'Tolong Isi Semua Data Yang Diminta';
    }
    else {
      if(ng.password == ng.rePassword) {
        ng.signUpHttp(ng.name, ng.username, ng.password).subscribe((data) => {
          if(data['status'] == "SUCCESS") {
            ng.signup.color = 'primary';
            ng.signup.message = 'Akun Telah Berhasil Didaftarkan';
          }
          else if(data['status'] == "ERROR") {
            ng.signup.color = 'danger';
            ng.signup.isError = true;
            ng.signup.message = data['message'];
          }
        });
      }
      else {
        ng.signup.color = 'danger';
        ng.signup.isError = true;
        ng.signup.message = 'Nilai password dan pengulangan password tidak sama';
      }
    }
  }
}
