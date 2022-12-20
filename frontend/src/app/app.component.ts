import { keyframes } from '@angular/animations';
import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';

const BASE_URL = 'http://localhost/';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  searchTerm = '';
  searchStart = false;
  users : string[] = [];
  loading = false;

  constructor(private http: HttpClient) { }

  searchUsers(q: string) {
    if (q.length > 1) {
      this.loading = true;
      this.searchStart = true;
      this.users.length = 0;
      this.http.get<string[]>(BASE_URL + 'searchUsers?q=' + q)
        .subscribe((data: any) => {          
          this.users = data;
          this.loading = false;
      });
    }
  }
}
