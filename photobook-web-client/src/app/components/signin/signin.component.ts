import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup} from "@angular/forms";
import {AuthStateService} from "../../shared/auth-state.service";
import {AuthService} from "../../shared/auth.service";
import {Router} from "@angular/router";
import {TokenService} from "../../shared/token.service";

@Component({
  selector: 'app-signin',
  templateUrl: './signin.component.html',
  styleUrls: ['./signin.component.scss']
})
export class SigninComponent implements OnInit {

  loginForm: FormGroup;
  errors = null;

  constructor(
    public router: Router,
    public fb: FormBuilder,
    public authService: AuthService,
    private token: TokenService,
    private authState: AuthStateService,
  ) {
    this.loginForm = this.fb.group({
      email: [],
      password: []
    })
  }

  ngOnInit() {
  }

  onSubmit() {
    this.authService.signIn(this.loginForm.value).subscribe(
      result => {
        this.responseHandler(result);
      },
      error => {
        this.errors = error.error;
      }, () => {
        this.authState.setAuthState(true);
        this.loginForm.reset()
        this.router.navigate(['profile']);
      }
    );
  }

  // Handle response
  responseHandler(data) {
    this.token.handleData(data.access_token);
  }

}
