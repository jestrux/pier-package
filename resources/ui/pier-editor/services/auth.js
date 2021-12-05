const USER_KEY = 'PIER_ADMIN_USER';
// import store from '../store';
export const saveUser = (user) =>
  Promise.resolve().then(() => localStorage.setItem(USER_KEY, JSON.stringify(user)))

export const getUser = () =>
  Promise.resolve().then(() => localStorage.getItem(USER_KEY))

export const getToken = () => Promise.resolve().then(async () => {
    return "";
//   var admin = store.state.authUser;
//   if(admin && admin.token){
//     return admin.token;
//   }
//   else{
//     let user = await getUser();
//     if(user){
//       user = JSON.parse(user);
//       return user.token;
//     }
//     return null;
//   }
})

export const removeUser = () =>
  Promise.resolve().then(() => localStorage.removeItem(USER_KEY))