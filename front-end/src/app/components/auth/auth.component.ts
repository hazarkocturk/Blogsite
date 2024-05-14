import { Component, Input, inject } from '@angular/core';
import { DialogModule } from 'primeng/dialog';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-auth',
  standalone: true,
  imports: [DialogModule, CommonModule, FormsModule],
  templateUrl: './auth.component.html',
  styleUrl: './auth.component.scss',
})
export class AuthComponent {
  @Input() display: boolean = false;
  authService = inject(AuthService);

  email: string = '';
  password: string = '';
  user = this.authService.getUser();

  onLogin = () => {
    this.authService
      .login({ email: this.email, password: this.password })
      .subscribe({
        next: () => (this.display = false),
        error: (err) => console.log(err),
      });
  };

  onLogOut = () => {
    this.authService.logout();
  };
}
