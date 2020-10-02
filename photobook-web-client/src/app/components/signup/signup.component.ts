import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import {FormBuilder, FormGroup} from "@angular/forms";
import {AuthService} from "../../shared/auth.service";

@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.scss']
})
export class SignupComponent implements OnInit {
  registerForm: FormGroup;
  errors = null;

  constructor(
    public router: Router,
    public fb: FormBuilder,
    public authService: AuthService
  ) {
    this.registerForm = this.fb.group({
      name: [''],
      email: [''],
      password: [''],
      password_confirmation: ['']
    })
  }

  ngOnInit() { }

  onSubmit() {
    this.authService.register(this.registerForm.value).subscribe(
      result => {
        console.log(result)
      },
      error => {
        this.errors = error.error;
        console.log(error.error)
      },
      () => {
        this.registerForm.reset()
        this.router.navigate(['login']);
      }
    )
  }

}
