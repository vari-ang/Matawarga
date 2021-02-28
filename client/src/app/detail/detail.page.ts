import { Component, OnInit } from '@angular/core';
import { AlertController } from '@ionic/angular';
import { ActivatedRoute	} from '@angular/router';
import { IncidentModel } from '../incident.model';
import { IncidentService } from '../incident.service';

@Component({
  selector: 'app-detail',
  templateUrl: './detail.page.html',
  styleUrls: ['./detail.page.scss'],
})
export class DetailPage implements OnInit {
  constructor(public alertController: AlertController, public route:ActivatedRoute, public is:IncidentService) { }

  id:number = 0; // Url param
  incident:IncidentModel = new IncidentModel(0, "", "", "", "", false, 0, [], 0, "", 0.0, 0.0);

  slideOptions = {
    initialSlide: 0,
    speed: 400
  };

  comment:string = "";

  ngOnInit() { }

  ionViewWillEnter() { 
    this.id = parseInt(this.route.snapshot.params['id']);
    this.getDetailIncident(this.id);
  }

  getDetailIncident(incidentId:number) {
    var ng = this;

    ng.is.getDetailIncidentHttp(incidentId).subscribe(
      (data) => {
        ng.incident = data;
    });
  }

  giveLike(incidentId) {
    var ng = this;

    ng.is.giveLikeHttp(incidentId).subscribe(
      (data) => {
        if(data['status'] == "SUCCESS") {
          if(data['message'] == "LIKED") {
            ng.incident.liked = true;
            ng.incident.jumlah_like += 1;
          }
          else if(data['message'] == "UNLIKED") {
            ng.incident.liked = false;
            ng.incident.jumlah_like -= 1;
          }
        }
        else if(data['status'] == "ERROR") {
          this.presentErrorAlert('Tidak Dapat Melakukan Like/ Dislike Postingan Ini', data['message']);
        }
    });
  }

  giveComment(incidentId) {
    var ng = this;

    ng.is.giveCommentHttp(incidentId, ng.comment).subscribe(
      (data) => {
        if(data['status'] == "SUCCESS") {
          // Add comment
          ng.incident.komens.push({
            username: localStorage.username,
            komentar: ng.comment
          });

          ng.incident.jumlah_komen += 1;

          // Remove comment
          ng.comment = '';
        }
        else if(data['status'] == "ERROR") {
          this.presentErrorAlert('Tidak Dapat Menambahkan Komentar', data['message']);
        }
    });
  }

  async presentErrorAlert(header, message) {
    const alert = await this.alertController.create({
      cssClass: 'my-custom-class',
      header: `${header}`,
      subHeader: 'Terjadi Kendala',
      message: `${message}`,
      buttons: ['OK']
    });

    await alert.present();
  }
}
