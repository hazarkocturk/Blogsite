import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Post } from '../../../types';
import { ButtonModule } from 'primeng/button';
import { FormsModule } from '@angular/forms';
import { PostsService } from '../../services/posts.service';

@Component({
  selector: 'app-edit-post',
  standalone: true,
  imports: [ButtonModule, FormsModule, PostsService],
  templateUrl: './edit-post.component.html',
  styleUrl: './edit-post.component.scss',
})
export class EditPostComponent {
  constructor(private postsService: PostsService) {}

  // @Input() header!: string;

  @Input() post: Post = {
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

  onConfirm = () => console.log(this.post);
  onCancel = () => console.log('Abort !!!');
}
