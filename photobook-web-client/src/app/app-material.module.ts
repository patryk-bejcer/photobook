import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';

import {MatButtonModule} from "@angular/material/button";
import {MatToolbarModule} from "@angular/material/toolbar";
import {MatCardModule} from "@angular/material/card";
import {MatNativeDateModule} from "@angular/material/core";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatCheckboxModule} from "@angular/material/checkbox";
import {MatIconModule} from "@angular/material/icon";
import {MatListModule} from "@angular/material/list";
import {MatInputModule} from "@angular/material/input";
import {FormsModule} from "@angular/forms";
import {MatRadioModule} from "@angular/material/radio";
import {MatDatepickerModule} from "@angular/material/datepicker";

@NgModule({
  declarations: [],
  imports: [
    MatNativeDateModule,
    MatDatepickerModule,
    MatIconModule,
    MatButtonModule,
    MatCheckboxModule,
    MatToolbarModule,
    FormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatListModule,
    MatRadioModule,
  ],
  exports: [
    MatNativeDateModule,
    FormsModule,
    MatDatepickerModule,
    MatIconModule,
    MatButtonModule,
    MatCheckboxModule,
    MatToolbarModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatListModule,
    MatRadioModule,
  ],
})
export class AppMaterialModule {
}
