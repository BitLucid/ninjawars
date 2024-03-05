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
  if (!seed === null || seed === undefined) throw new Error('seededInt requires a seed');
  const r = seededRandom(seed);
  const minL = Math.ceil(min);
  const maxL = Math.floor(max);
  return Math.floor(r * (maxL - minL + 1)) + minL;
};

export const seededString = (seed, size = 20) => {
  if (!seed === null || seed === undefined) throw new Error('seededString requires a seed');
  let text = '';
  const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ';
  // create a string based on the seed, length
  const total = size ?? 5;
  for (let i = 0; i < total; i += 1) {
    text += possible.charAt(Math.floor(seededRandom(seed + i) * possible.length));
  }
  return text;
};

/**
 * generate a numeric seed that is constant for each visit
 * @returns {number} A stabilized Math.random() equivalent
 */
export const generateStableSeed = () => {
  const seed = Math.random();
  const cachedSeed = localStorage.getItem('stableSingleUtilSeed');
  if (!cachedSeed) localStorage.setItem('stableSingleUtilSeed', seed);
  return cachedSeed || seed;
};

/**
 * Give an array of stable seeded ints
 * @param {Number} num
 * @returns {Array<Number>}
 */
export const variantStableSeeds = (num) => {
  const seeds = Array(num).fill().map(() => Math.floor(Math.random() * 100 * num));
  const cachedSeeds = localStorage.getItem('variantStableSeeds');
  if (!cachedSeeds) localStorage.setItem('variantStableSeeds', JSON.stringify(seeds));
  return cachedSeeds ? JSON.parse(cachedSeeds) : seeds;
};
