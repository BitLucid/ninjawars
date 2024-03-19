/**
 * A simple wrapper for the rpc-like nw api
 */

/**
 * @TODO turns, message count, etc
 * Don't worry about the chats api too much as we're moving away from that
 */

const apiMethods = {
  nextTarget: (data) => fetch(`/api?type=nextTarget&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  deactivateChar: (data) => fetch(`/api?type=deactivateChar&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  reactivateChar: (data) => fetch(`/api?type=reactivateChar&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  self: (data) => fetch(`/api?type=player&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  player: (data) => fetch(`/api?type=player&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  chats: (data) => fetch(`/api?type=chats&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  clans: (data) => fetch(`/api?type=clans&json=1&data=${encodeURIComponent(JSON.stringify(data))}`),
  newChats: (data) => fetch(`/api?type=newChats&json=1&since=${encodeURIComponent(JSON.stringify(data))}`),
  sendChat: (message) => fetch(`/api?type=send_chat&json=1&msg=${encodeURIComponent(JSON.stringify(message))}`),
  inventory: () => fetch('/api?type=inventory&json=1'),
  playerCount: () => fetch('/api?type=playerCount&json=1'),
  homepage: () => fetch('/api?type=index&json=1'),
  latestMessage: () => fetch('/api?type=latestMessage&json=1'),
  latestChatId: () => fetch('/api?type=latestChatId&json=1'),
  latestEvent: () => fetch('/api?type=latestEvent&json=1'),
  charSearch: (term, limit) => fetch(`/api?type=char_search&json=1&term=${encodeURIComponent(JSON.stringify(term))}&limit=${encodeURIComponent(JSON.stringify(limit))}`),
  sendCommunications: () => fetch('/api?type=sendCommunications&json=1'),
};

/*
 * Expose the api methods
 * @usage api.getNextTarget(data)
 */
export default apiMethods;
