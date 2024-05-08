import { Component } from '@angular/core';
import { ArticlesService } from '../services/articles.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss',
})
export class HomeComponent {
  constructor(private articlesService: ArticlesService) {}

  ngOnInit() {
    this.articlesService.getArticles("http://localhost:3001/articles").subscribe((articles) => {
      console.log(articles);
    });
  }
}
