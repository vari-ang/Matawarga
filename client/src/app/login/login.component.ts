import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpParams } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent implements OnInit {
  constructor(private http: HttpClient, public router: Router) { }

  username:string = '';
  password: string = '';
  login = {
    color: 'danger',
    isError: false,
    message: ''
  }

  ngOnInit() {}

  getUsername(event) {
    this.username = event.target.value;
  }

  getPassword(event) {
    this.password = event.target.value;
  }

  logInHttp(username:string, password:string):Observable<any> {
    let body = new HttpParams();
    body = body.set('username', username);
    body = body.set('password', password);
  	return this.http.post("http://ubaya.prototipe.net/s160717023/matawarga/login.php", body);
  }

  logIn() {
    var ng = this; 

    if(!ng.username || !ng.password) {
      ng.login.color = 'danger';
      ng.login.isError = true;
      ng.login.message = 'Tolong Isi Semua Data Yang Diminta';
    }
    else {
      ng.logInHttp(ng.username, ng.password).subscribe((data) => {
        if(data['status'] == "SUCCESS") {
          localStorage.username = ng.username;

          ng.login.color = 'primary';
          ng.login.message = 'Berhasil Login';
          ng.router.navigate(['tabs/']);

          // Clear data
          // ng.username = '';
          // ng.password = '';
          ng.login.color = 'danger';
          ng.login.isError = false;
          ng.login.message = '';
        }
        else if(data['status'] == "ERROR") {
          ng.login.color = 'danger';
          ng.login.isError = true;
          ng.login.message = data['message'];
        }
      });
    }
  }
}
