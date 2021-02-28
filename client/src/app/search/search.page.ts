import { Component, OnInit } from '@angular/core';
import { IncidentService } from '../incident.service';

@Component({
  selector: 'app-search',
  templateUrl: './search.page.html',
  styleUrls: ['./search.page.scss'],
})
export class SearchPage implements OnInit {
  constructor(public is:IncidentService) { }

  searchResults = [];

  ngOnInit() {  }

  getInput(event) {
    this.getSearchResults(event.target.value)
  }

  getSearchResults(searchText) {
    var ng = this;

    ng.is.searchIncident(searchText).subscribe(
      (data) => {
        ng.searchResults = data;
    });
  }
}
