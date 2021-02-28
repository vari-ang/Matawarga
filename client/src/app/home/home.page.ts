import { Component, OnInit } from '@angular/core';
import { IncidentService } from '../incident.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
})
export class HomePage implements OnInit {
  constructor(public is:IncidentService) { }

  username = localStorage.username;

  incidents = [];

  ionViewWillEnter() { this.getIncidents(); }
  
  ngOnInit() { }

  getIncidents() {
    var ng = this;

    ng.is.getIncidentsHttp().subscribe(
      (data) => {
        ng.incidents = data;
    });
  }

  giveLike(incidentId, ix) {
    var ng = this;

    ng.is.giveLikeHttp(incidentId).subscribe(
      (data) => {
        if(data['status'] == "SUCCESS") {
          if(data['message'] == "LIKED") {
            ng.incidents[ix].liked = true;
            ng.incidents[ix].jumlah_like += 1;
          }
          else if(data['message'] == "UNLIKED") {
            ng.incidents[ix].liked = false;
            ng.incidents[ix].jumlah_like -= 1;
          }
        }
    });
  }
}
