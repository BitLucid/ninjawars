{literal}  
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'search',
  search: 'ninjawars',
  interval: 14000,
  title: '',
  subject: 'NinjaWars!',
  width: 'auto',
  height: 300,
  theme: {
    shell: {
      background: '#01070a',
      color: '#d91414'
    },
    tweets: {
      background: '#e6e1e6',
      color: '#444444',
      links: '#1985b5'
    }
  },
  features: {
    scrollbar: true,
    loop: false,
    live: false,
    hashtags: true,
    timestamp: true,
    avatars: true,
    toptweets: true,
    behavior: 'all'
  }
}).render().start();
</script>
{/literal}
