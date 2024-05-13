import { Component } from '@angular/core';
import { Post, Posts } from '../../types';
import { CardComponent } from '../components/card/card.component';
import { PostsService } from '../services/posts.service';
import { CommonModule } from '@angular/common';
import { EditPopupComponent } from '../components/edit-popup/edit-popup.component';
import { ButtonModule } from 'primeng/button';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CardComponent, CommonModule, EditPopupComponent, ButtonModule, ],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss',
})
export class HomeComponent {
  constructor(private postsService: PostsService) {}

  posts: Post[] = [];

  displayEditPopup: boolean = false;
  displayAddPopup: boolean = false;

  toggleEditPopup = (post: Post) => {
    this.selectedPost = post;
    this.displayEditPopup = true;
  };

  toggleDeletePopup = (post: Post) => {};

  toggleAddPopup = () => (this.displayAddPopup = true);

  selectedPost: Post = {
    id: 0,
    category: '',
    image: [''],
    title: '',
    date: '',
    content: '',
    slug: '',
    views: 0,
    likes: 0,
  };

  onConfirmEdit = (post: Post) => {
    if (!this.selectedPost.id) {
      return;
    }
    this.editPosts(post, this.selectedPost.id);
    this.displayEditPopup = false;
  };

  onConfirmAdd = (post: Post) => {
    this.addPosts(post);
    this.displayEditPopup = false;
  };

  /********************
   *                  *
   *  Querries to DB  *
   *                  *
   ********************/
  fetchPosts() {
    this.postsService.getPosts('http://localhost:3001/posts').subscribe({
      next: (posts: Posts) => {
        this.posts = posts.posts;
      },
      error: (error) => {
        console.log(error);
      },
    });
  }
  editPosts(post: Post, id: number) {
    this.postsService
      .editPosts(`http://localhost:3000/posts/${id}`, post)
      .subscribe({
        next: (data) => {
          console.log(data);
          this.fetchPosts();
        },
        error: (error) => {
          console.log(error);
        },
      });
  }
  addPosts(post: Post) {
    this.postsService.addPosts('http://localhost:3001/posts', post).subscribe({
      next: (data) => {
        console.log(data);
        this.fetchPosts();
      },
      error: (error) => {
        console.log(error);
      },
    });
  }
  deletePosts(id: number) {
    this.postsService
      .deletePosts(`http://localhost:3000/posts/${id}`)
      .subscribe({
        next: (data) => {
          console.log(data);
          this.fetchPosts();
        },
        error: (error) => {
          console.log(error);
        },
      });
  }

  onCardOutput(post: Post) {
    console.log(post, 'output');
  }

  ngOnInit() {
    this.fetchPosts();
  }
}