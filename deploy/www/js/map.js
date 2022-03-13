
import api from '/js/api.js';

// Place a characer from the template to the grid
export const placeNearby = (target) => {
  const template = document.querySelector('template')[0];
  template.querySelector('img').src = target.image;
  template.querySelector('span').innerText = target.name;
  const clone = template.content.cloneNode(true);
  document.querySelector('.nearby-characters .gridded').appendChild(clone);
}
// get targets from api
export const getTargets = () => {
  (new api).nearbyList(data).then(response => {
    response.forEach(target => {
      placeNearby(target);
    });
  });
}

