import { Component, OnInit } from '@angular/core';
import {AuthService, User} from "../../shared/auth.service";

@Component({
  selector: 'app-user-profile',
  templateUrl: './user-profile.component.html',
  styleUrls: ['./user-profile.component.scss']
})
export class UserProfileComponent implements OnInit {

  UserProfile: User;

  constructor(
    public authService: AuthService
  ) {
    this.authService.profileUser().subscribe((data:any) => {
      this.UserProfile = data;
    })
  }

  ngOnInit() { }

}
