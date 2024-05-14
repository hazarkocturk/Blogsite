import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Post } from '../../../types';
import { ButtonModule } from 'primeng/button';
import { FormsModule } from '@angular/forms';
import { PostsService } from '../../services/posts.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-edit-post',
  standalone: true,
  imports: [ButtonModule, FormsModule],
  templateUrl: './edit-post.component.html',
  styleUrl: './edit-post.component.scss',
})
export class EditPostComponent {
  constructor(
    private router: Router,
    private postsService: PostsService,
    private route: ActivatedRoute
  ) {}

  post: Post = {
    category: '',
    image: [''],
    title: '',
    date: '',
    content: '',
    slug: '',
    views: 0,
    likes: 0,
  };

  @Output() confirm = new EventEmitter<Post>();

  header: string = 'New Post';

  getPost(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) {
      this.postsService
        .getPostById('http://localhost:3001/posts/' + id)
        .subscribe({
          next: (post: Post) => {
            this.post = post;
            this.header = 'Edit Post';
          },
          error: (error) => {
            console.log(error);
          },
        });
    }
  }

  ngOnInit() {
    this.getPost();
  }

  onConfirm = () => {
    console.log(this.post);
    this.postsService
      .addPosts('http://localhost:3001/posts', this.post)
      .subscribe({
        next: (data) => console.log('new post created:', data),
        error: (err) => console.log(err),
      });
  };
  onCancel = () => console.log('Abort !!!');
}
