import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

// User interface
export class User {
  name: string;
  email: string;
  password: string;
  password_confirmation: string
}

@Injectable({
  providedIn: 'root'
})

export class AuthService {


  constructor(private http: HttpClient) {}

  // Register user.
  register(user: User) : Observable<any> {
    return this.http.post('http://127.0.0.1:8000/api/auth/register', user);
  }

  // Login
  signIn(user: User): Observable<any> {
    return this.http.post<any>('http://127.0.0.1:8000/api/auth/login', user);
  }

  // Access user profile
  profileUser(): Observable<any> {
    return this.http.get('http://127.0.0.1:8000/api/auth/user-profile');
  }
}
