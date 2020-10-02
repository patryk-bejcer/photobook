import {Component, OnInit} from '@angular/core';
import {Router} from "@angular/router";
import {FormBuilder, FormControl, FormGroup, FormGroupDirective, NgForm, Validators} from "@angular/forms";
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
      name: new FormControl('', [Validators.required, Validators.minLength(6)]),
      email: new FormControl('', [Validators.required, Validators.email]),
      password: new FormControl('', [Validators.required, Validators.minLength(6)]),
      password_confirmation: new FormControl('', [Validators.required, Validators.minLength(6)]),
    })
  }

  ngOnInit() {
  }

  onSubmit() {
    this.authService.register(this.registerForm.value).subscribe(
      result => {
        console.log(result)
      },
      error => {
        this.errors = error.error;
        for (const fieldError in this.errors) {
          if (this.errors[fieldError]) {
            this.registerForm.controls[fieldError].setErrors(
              {invalid: this.errors[fieldError]['message']}
            );
          }
        }
      },
      () => {
        this.registerForm.reset()
        this.router.navigate(['login']);
      }
    )
  }

}
