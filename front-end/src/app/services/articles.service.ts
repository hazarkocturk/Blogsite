import { Injectable } from '@angular/core';
import { ApiService } from './api.service';
import { Observable } from 'rxjs';
import { Articles } from '../../types';

@Injectable({
  providedIn: 'root'
})
export class ArticlesService {

  constructor(private apiService: ApiService) { }

  getArticles = (
    url: string,
  ): Observable<Articles> => {
    return this.apiService.get(url);
  };
}
