import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot } from '@angular/router';

@Injectable({
  providedIn: "root"
})
export class AuthGuardService implements CanActivate {
  constructor (public router: Router) { }

  canActivate(route: ActivatedRouteSnapshot):boolean {
    // console.log(route);

    if (!localStorage.username) {
      this.router.navigate(['login']);
      return false;
    }

    return true;
  }
}
