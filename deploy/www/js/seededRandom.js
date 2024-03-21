/**
 * Takes a number and turns it into the math.random equivalent with Sin
 * @param {number} seed
 * @returns {number} Sin Equivalent to Math.random
 */
export function seededRandom(seed) {
  // Just have to avoid 0 to feed into Math.sin
  const x = Math.sin(seed + 1) * 10000;
  // Throw away 10, 100, 1000 to avoid patterns
  return x - Math.floor(x);
}

/**
 * Return ints, by default 1 to 99, depends on localStorage
 * @param {number} seed
 * @param {number} min
 * @param {number} max
 * @returns {number}
 */
export const seededInt = (seed, min = 1, max = 99) => {
  if (seed === null || seed === undefined) throw new Error('seededInt requires a seed');
  const ran = seededRandom(seed);
  const minL = Math.ceil(min);
  const maxL = Math.floor(max);
  return Math.floor(ran * (maxL - minL + 1)) + minL;
};

/**
 *
 * @param {*} seed
 * @param {*} size
 * @returns
 */
export const seededString = (seed, size = 20, rangeMin = null, options = {}) => {
  const { spaces = true } = options ?? {};
  if (seed === null || seed === undefined) throw new Error('seededString requires a seed');
  let text = '';
  const possible = `ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789${spaces ? '      ' : ''}`;
  // create a string based on the seed, length
  let total = size ?? 5;
  // If rangeMin is set, seed a variant of a seeded length
  if (rangeMin !== null) {
    total = seededRandom(seed) * (size - rangeMin) + rangeMin;
  }
  for (let i = 0; i < total; i += 1) {
    text += possible.charAt(Math.floor(seededRandom(seed + i) * possible.length));
  }
  return text;
};

/**
 * Up to 24 characters and as low as 3 are allowed in the system
 * Dashes and underscores are not handled by this
 * 50% of ninja names will be less than 12 char, but sometimes will be longer
 */
export const seededNinjaName = (seed) => {
  const upperBound = seededRandom(seed) < 0.5 ? 12 : 24;
  return seededString(seed, upperBound, 3, { spaces: false });
};

export const seededSize = (seed, min = 1, max = 99) => {
  if (seed === null || seed === undefined) throw new Error('seededSize requires a seed');
  const ran = seededRandom(seed);
  const minL = Math.ceil(min);
  const maxL = Math.floor(max);
  return Math.floor(ran * (maxL - minL + 1)) + minL;
};

/**
 * generate a numeric seed that is constant for each visit
 * @returns {number} A stabilized Math.random() equivalent
 */
export const generateStableSeed = () => {
  const seed = Math.random();
  const cachedSeed = JSON.parse(localStorage.getItem('stableSingleUtilSeed'));
  if (!cachedSeed) localStorage.setItem('stableSingleUtilSeed', JSON.stringify(seed));
  return cachedSeed || seed;
};

/**
 * on-the-fly generate a stable set of ints by the seed
 * @param {Number} num
 * @returns {Array<Number>}
 */
export const variantStableSeeds = (num) => {
  const sinSeed = generateStableSeed();
  return Array(num).fill().map((_, i) => Math.sin(sinSeed + i) * 10000);
};
