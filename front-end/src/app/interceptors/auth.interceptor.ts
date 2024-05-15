import { HttpInterceptorFn } from '@angular/common/http';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const jwtToken = getJwtToken();
  if(jwtToken){
    let cloned = req.clone({
      setHeaders: {
        Authorization: `Bearer ${jwtToken}`,
        'Content-Type':'application/json',
        'Access-Control-Allow-Origin':"*"
      },
    });
    return next(cloned);

  }else{
    let cloned = req.clone({
      setHeaders: {
        'Content-Type':'application/json',
        'Access-Control-Allow-Origin':" https://blogdbhazar-nico-5d30f5ae698b.herokuapp.com "
      },
    });
    return next(cloned);
  }
  
};

function getJwtToken ():string | null {
  console.log("getting jwt")
  return localStorage.getItem('JWT_TOKEN');
}