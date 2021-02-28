import { Injectable } from '@angular/core';
import { IncidentModel } from './incident.model';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpParams } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class IncidentService {
  constructor(private http: HttpClient) { }

  incidents: IncidentModel[] = [];
  serverLoc = 'http://ubaya.prototipe.net/s160717023/matawarga';

  getIncidentsHttp():Observable<any> {
    return this.http.get(`${this.serverLoc}/home.php?username=${localStorage.username}`);      
  }

  getDetailIncidentHttp(incidentId):Observable<any> {
    return this.http.get(`${this.serverLoc}/detail_incident.php?id=${incidentId}&username=${localStorage.username}`);      
  }

  giveLikeHttp(incidentId):Observable<any> {
    return this.http.get(`${this.serverLoc}/like_incident.php?idkejadian=${incidentId}&username=${localStorage.username}`);      
  }

  giveCommentHttp(incidentId, comment:string):Observable<any> {
    let body = new HttpParams();
    body = body.set('username', localStorage.username);
    body = body.set('idkejadian', incidentId);
    body = body.set('comment', comment);
    return this.http.post(`${this.serverLoc}/add_comment.php`, body);      
  }

  searchIncident(text):Observable<any> {
    return this.http.get(`${this.serverLoc}/search.php?q=${text}`);      
  }

  addIncident(frmData):Observable<any> {
    return this.http.post(`${this.serverLoc}/add_incident.php`, frmData);      
  }
}
