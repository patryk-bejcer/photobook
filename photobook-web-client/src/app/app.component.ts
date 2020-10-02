import {Component} from '@angular/core';
import {AuthStateService} from "./shared/auth-state.service";
import {Router} from "@angular/router";
import {TokenService} from "./shared/token.service";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  isSignedIn: boolean;

  constructor(
    private auth: AuthStateService,
    public router: Router,
    public token: TokenService,
  ) {
  }

  ngOnInit() {
    this.auth.userAuthState.subscribe(val => {
      this.isSignedIn = val;
    });
    console.log(this.isSignedIn)
    console.log(localStorage.getItem('auth_token'))
  }

  // Sign out.
  signOut() {
    this.auth.setAuthState(false);
    this.token.removeToken();
    this.router.navigate(['login']);
  }
}
