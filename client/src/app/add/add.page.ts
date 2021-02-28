import { Component, OnInit } from '@angular/core';
import { AlertController } from '@ionic/angular';
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { IncidentService } from '../incident.service';

@Component({
  selector: 'app-add',
  templateUrl: './add.page.html',
  styleUrls: ['./add.page.scss'],
})
export class AddPage implements OnInit {
  constructor(public alertController: AlertController, public geo:Geolocation, public is:IncidentService) { }

  incident = {
    title: '',
    description: '',
    location: {
      latitude: 0.0,
      longitude: 0.0
    },
    photos: [],
    institute: '',
    datetime: ''
  }

  ngOnInit() { 
    var ng = this;

    ng.incident.datetime = ng.getCurrentDatetime();

    ng.geo.getCurrentPosition().then((resp) => {
      ng.incident.location.latitude = resp.coords.latitude;
      ng.incident.location.longitude = resp.coords.longitude; 
    }).catch((error) => {
      ng.presentAlert('Tidak Dapat Menampilkan Lokasi Anda', 'Terjadi Kendala', error);
    });
  }

  getCurrentDatetime() {
    var dateObj = new Date(),
        year = dateObj.getFullYear(),
        month = String(dateObj.getMonth() + 1).padStart(2, '0'),
        day = String(dateObj.getDate()).padStart(2, '0'),
        h = String(dateObj.getHours()).padStart(2, '0'),
        m = String(dateObj.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${h}:${m}:00-00:00`;
  }

  changeListener($event):void {
    var ng = this;
    var isError = false;

    for (var file of $event.target.files) {
      if(file.size >= 200000) { isError = true; ng.incident.photos = []; break; }
      else { ng.incident.photos.push(file); }
    }

    if(isError) {
      ng.presentAlert('Ukuran Gambar Terlalu Besar', 
                      'Gambar Harus Berukuran < 200KB', 
                      'Mohon upload kembali seluruh gambar dengan ukuran yang sesuai'); 
    }
  }

  async presentAlert(header, subheader, message) {
    const alert = await this.alertController.create({
      cssClass: 'my-custom-class',
      header: `${header}`,
      subHeader: `${subheader}`,
      message: `${message}`,
      buttons: ['OK']
    });

    await alert.present();
  }

  addProduct() {
    var ng = this;

    var formAddIncidentObj = new FormData();
    formAddIncidentObj.append("username", localStorage.username);
    formAddIncidentObj.append("title", ng.incident.title);
    formAddIncidentObj.append("description", ng.incident.description);
    formAddIncidentObj.append("institute", ng.incident.institute);
    formAddIncidentObj.append("datetime", ng.incident.datetime);
    formAddIncidentObj.append("longitude", `${ng.incident.location.longitude}`);
    formAddIncidentObj.append("latitude", `${ng.incident.location.latitude}`);
    for(var photo of ng.incident.photos) { 
      formAddIncidentObj.append("photo[]", photo); 
    }

    ng.is.addIncident(formAddIncidentObj).subscribe(
      (data) => {
        if(data['status'] == "SUCCESS") {
          ng.presentAlert('Berhasil Menambahkan Laporan Kejadian', 'Sukses', 'Terima kasih atas laporan Anda'); 

          // Clear input
          ng.incident.title = '';
          ng.incident.description = '';
          ng.incident.photos = [];
          ng.incident.institute = '';
          ng.incident.datetime = ng.getCurrentDatetime();
        }
        else if(data['status'] == "ERROR") {
          ng.presentAlert('Tidak Dapat Menambahkan Laporan Kejadian', 'Terjadi Kendala', data['message']); 
        }
    });
  }
}
