import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { HttpClientService } from './http-client.service';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private readonly JWT_TOKEN = 'JWT_TOKEN';
  private loggedUser?: string;
  private IsAuthenticatedSubject = new BehaviorSubject<boolean>(false);



  constructor(private http : HttpClientService) {}

  login(user: { email: string; password: string }): Observable<any> {
    return this.http
      .post('https://blogdbhazar-nico-5d30f5ae698b.herokuapp.com/api/auth/login', user)
      .pipe(
        tap((tokens) => {
          this.doLoginUser(user.email, tokens);

          console.log('tokens', tokens);
          console.log('user', user);
        })
      );
  }
  register(user: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    userType: string;
  }): Observable<any> {
    console.log(user)
    return this.http.post("https://blogdbhazar-nico-5d30f5ae698b.herokuapp.com/api/auth/register", user)
  }

  private doLoginUser(username: string, tokens: any) {
    this.loggedUser = username;
    console.log('loggedUser', this.loggedUser);
    this.storeJwtToken(tokens.token);
    this.IsAuthenticatedSubject.next(true);
  }

  private storeJwtToken(jwt: string) {
    localStorage.setItem(this.JWT_TOKEN, jwt);
  }

  logout() {
    localStorage.removeItem(this.JWT_TOKEN);
    this.IsAuthenticatedSubject.next(false);
  }

  getStuff = () => {
    let token = localStorage.getItem(this.JWT_TOKEN);
    return this.http.get(
      'https://lokkeroom-7168807cbabe.herokuapp.com/api/user/lobbies'
    );
  };

  // getCurrentAuthUser = () => {

  // }

  isLoggedIn() {
    return !!localStorage.getItem(this.JWT_TOKEN);
  }

  getUser() {
    console.log('loggedUser', this.loggedUser);
    return this.loggedUser;
  }
}
