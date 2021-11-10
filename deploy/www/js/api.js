/**
 * A simple wrapper for the rpc-like nw api
 */

const apiMethods = {
    // eslint-disable-next-line indent
    nextTarget: (data) => fetch(`/api?type=nextTarget&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
    deactivateChar: (data) => fetch(`/api?type=deactivate_char&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
};

/*
 * Expose the api methods
 * @usage api.getNextTarget(data)
 */
export default apiMethods;
