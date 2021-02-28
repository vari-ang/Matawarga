import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot } from '@angular/router';

@Injectable({
  providedIn: "root"
})
export class LoginGuardService implements CanActivate {
  constructor (public router: Router) { }

  canActivate(route: ActivatedRouteSnapshot):boolean {
    if (localStorage.username) {
      this.router.navigate(['tabs/home']);
      return false;
    }
     
    return true;
  }
}
