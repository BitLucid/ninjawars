          <div id='index-news'>
              <div id="news" class="boxes active">
                <div class="box-title centered">
                  News
                </div>
                {if isset($latest_news) and $latest_news}
                <p>{$latest_news}</p>
                {else}
                <p>There is no news</p>
                {/if}
                <div class="news-switch centered">
                  <a id='full-news-link' href="news.php" target="main">
                    View all news
                  </a>
                </div>
              </div>
          </div> <!-- End of index-news --> 
