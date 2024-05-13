import { Component, EventEmitter, Output } from '@angular/core';
import { PostsService } from '../../services/posts.service';
import { ActivatedRoute } from '@angular/router';
import { Post } from '../../../types';
import { ButtonModule } from 'primeng/button';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-post',
  standalone: true,
  imports: [ButtonModule],
  templateUrl: './post.component.html',
  styleUrl: './post.component.scss'
})
export class PostComponent {

  @Output() edit: EventEmitter<Post> = new EventEmitter<Post>();
  @Output() delete: EventEmitter<Post> = new EventEmitter<Post>();
  
  constructor(
    private postsService: PostsService,
    private route: ActivatedRoute,
    ) {}
  
  post:Post = {
    id: 0,
    category: '',
    image: [''],
    title: '',
    date: '',
    content: '',
    slug: '',
    views: 0,
    likes: 0,
  }

  editPost = () => console.log("edit");
  deletePost = () => console.log("delete");

  getPost():void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.postsService.getPostById("http://localhost:3001/posts/"+id)
    .subscribe({
      next: (post: Post) => {
        this.post = post;
      },
      error: (error) => {
        console.log(error);
      },
    });
  }

  ngOnInit() {
    this.getPost();
  }
}
