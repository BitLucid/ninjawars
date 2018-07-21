--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4 (Ubuntu 10.4-2.pgdg16.04+1)
-- Dumped by pg_dump version 10.4 (Ubuntu 10.4-2.pgdg16.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: accounts; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.accounts (account_id, account_identity, phash, active_email, type, operational, created_date, last_login, last_login_failure, karma_total, last_ip, confirmed, verification_number, oauth_provider, oauth_id) FROM stdin;
28163	tchalvaks.pam@gmail.com	$2a$10$phx2W7lpcxSyNIivjbQplecU4PpdqzCwyAZlaKXCtSSd73cvV8hQG	tchalvaks.pam@gmail.com	0	t	2015-10-01 22:57:40.875048-04	\N	\N	0	33.15.180.33	1	3369	\N	\N
28252	tchal.vak.spam@gmail.com	$2a$10$6MYsAJ07vSN9N259WXQl7e41Caeccfs4y4Td4xahxyt6flBQQ1V2i	tchal.vak.spam@gmail.com	0	t	2015-11-28 09:30:42.12466-05	2015-11-28 09:30:55.56507-05	\N	0	33.53.191.33	1	8497	\N	\N
26013	tchalvakspam+test@gmail.com	$2a$10$LkkotOmYd7wYzxPQ6aE0iOuFxoJ.Ca/J33j9FcYW11i2akCjNzH7O	tchalvakspam+test@gmail.com	0	t	2013-05-28 12:08:59.643674-04	\N	\N	0	33.26.10.33	0	5951	\N	\N
27323	tchalvakspa.m@gmail.com	$2a$10$HPM59F/s0bmJrZLyTzTxv.mRd0Jelbj5J.CJqvNN9j9uIebmszkaS	tchalvakspa.m@gmail.com	0	t	2014-09-04 17:42:03.965945-04	2014-09-04 17:50:05.889621-04	\N	0	33.1.21.33	1	5453	\N	\N
26910	tchalvak.s.pam@gmail.com	$2a$10$wcVoZpXzf5AVl0HoCZ5JY.VFeqv5BhnYgzEOpp7SJODo9ub8ulBwC	tchalvak.s.pam@gmail.com	0	t	2014-02-17 07:39:37.21166-05	\N	\N	0	33.55.10.33	1	2014	\N	\N
26720	tchal.v.a.k.spam@gmail.com	$2a$10$WPD25Uv.yIquovJ5.42uKumRUXTykU/94dttnFI6xaIfUD3xHhkxq	tchal.v.a.k.spam@gmail.com	0	t	2013-11-28 15:10:45.997622-05	\N	2013-11-28 15:11:21.34459-05	0	33.1.155.33	0	7419	\N	\N
28407	tchalvak.s.p.am+nw@gmail.com	$2a$10$dZZALKaKBaiqrs5nPQdzveUDl82WNLhOvFxqSeZjJqT9kyPH.x7..	tchalvak.s.p.am+nw@gmail.com	0	t	2016-03-20 17:51:27.784993-04	2016-03-20 17:52:10.996148-04	\N	0	33.50.115.33	1	9970	\N	\N
45	tchalvak.spam@gmail.com	$2a$10$lRHt9qDO7dM0DZQDcJp5VehmK4K1kYd09oP4cU9ccYkPKs6MaFxju	tchalvak.spam@gmail.com	0	t	2007-12-01 19:24:12.858532-05	2016-05-16 14:46:54.937963-04	2016-05-16 14:46:54.948206-04	12	33.12.129.33	1	6270	\N	\N
2948	test2948@example.com	$2a$10$uAJYlOiyU1Ap5p/QtamNBebHcVAqvRxCXwvUU.l8q.vif1EMspBmG	test2948@example.com	0	t	2010-02-18 09:20:17.319739-05	2016-11-11 10:15:43.904222-05	2016-11-11 10:15:43.914287-05	8	33.51.26.33	1	419826	\N	\N
19863	tchalvakspam@gmail.com	$2a$10$GHma7d4DcoT.Iy40Hif0DeiaL5/BoHx2QDC5gZaYCVYXNmlbqOaWm	tchalvakspam@gmail.com	0	t	2011-12-29 10:37:03.665663-05	2011-12-29 10:37:39.999563-05	2018-02-28 10:31:43.142687-05	0	33.33.247.33	1	878066	\N	\N
5442	tchalvak@gmail.com	$2a$10$6YEa57NdkkkC8JEs8blWTeob/jrETMf7fHFZ.dZet/ppt9C5bSrxS	tchalvak@gmail.com	0	t	2009-11-03 07:04:53.223615-05	2018-07-21 14:44:32.529983-04	2018-07-21 14:44:23.262263-04	7	33.48.228.33	1	2345234	\N	\N
\.


--
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.news (news_id, title, content, created, updated, tags) FROM stdin;
1	New Release! Ninjawars v1.8.3	The latest release is now online! Lots of work on npcs to make them more complex and balanced. Work on accounts to prepare to allow multiple ninja per account! Ninja now have a lot of extra detail that can be filled in to flesh out your character, ninja description, traits, goals, instincts, etc. Faster & Better Chat. And lots of other individual changes: http://ninjawars.proboards.com/thread/1266/ninjawars-version-pre-release-testing?page=1&scrollTo=13884	2015-09-18 22:20:26.108667-04	2015-09-18 22:20:26.108667-04	releases,new features,ninja traits,npcs
2	Update!  Bounty capped, avatar menu, clan fixes.	Balance changes:  Bounty was able to go too high, now it is capped, and raises slower.\r\n===========================\r\nSite layout:  Now there's an avatar menu on the top right, ninja stats and account stuff lives there.  Clan page cleaned up a little.\r\n===========================\r\nBug fixes: Clan kicking fixed.	2015-10-01 14:43:16.663617-04	2015-10-01 14:43:16.663617-04	fixes,balance,
3	Clan functionalities redone.  Joining is simpler.	Clan functionalities refactored & cleaned up, joining is easier, more use of the Clan object, ClanController came into being.\r\n\r\nDoshin refactored.\r\nShop refactored.\r\n\r\nMessages area layout improved.\r\n\r\nMobile: Login & Signup button sizes fixed.\r\n\r\nSplash: Top logo layout improved some.\r\n\r\nNinja-box/ninja-avatar: Does stuff on hover to show that it's a clickable thing.	2015-10-21 10:45:01.457241-04	2015-10-21 10:45:01.457241-04	clan,update
4	NW v 1.8.8: Messages and Avatars, Turns over 9,000.	Personal/Clan Messages get shorter and more readable.  Avatars show after attacking, using an item, or using a skill on someone.\r\n\r\nYou can checkout the release notes on github here:\r\n\r\nhttps://github.com/BitLucid/ninjawars/releases/tag/v1.8.8\r\n\r\nAnd I forgot to make a news post for the previous release here:\r\n\r\nhttps://github.com/BitLucid/ninjawars/releases/tag/v1.8.7\r\n\r\nThe main thing is that the max level of the game is now reachable.\r\n	2015-11-29 01:34:53.349363-05	2015-11-29 01:34:53.349363-05	avatars,minor,fixes
5	Release v.1.8.9 - Login abstraction, more frequent stats updates, and stability fixes	Quickstats: Updated on most pages now, so you can see when things change even while you change when you see. Dawg.\r\nMap: colors added to all the locations.\r\nFight: Link on bottom of map to the fight.\r\nNpcs: the abstract npcs become a little more fun, more to come.\r\nError messages a little more readable.\r\nMake player names a little more readable.\r\nChanges to the login system internally.\r\nBetter testing of the pages now that they have controllers and login is injectable.\r\nClan: More object-oriented cleanup.\r\nAccount: Some internal cleanup.	2015-12-26 12:43:12.436566-05	2015-12-26 12:43:12.436566-05	quickstats, stability, release, login
6	Release 1.8.12: Pretty urls, a front controller, and more flexibility incoming!	Routing: ninjawars now uses pretty urls and a front controller.\r\nRouting: Lots of minor route changes with pretty urls, though we tried for backwards compatibility!\r\nShop/Casino/Doshin/Stats/Accounts list numbers more readably.\r\nDoshin: Prevent setting bounty on yourself.\r\nMap: Map squares get some tiling background images for now.\r\njs and css get updated reliably.\r\nDefault numbers (e.g. how many turns you want to work) on frontend are kept by js localstorage now.\r\nLogin: Refactored to use a front controller.\r\nAutoloading: We autoload controllers and other objects now!  ??maybe things will be faster??\r\nWe now use php 5.6 ... ...rockin' it oldschool\r\n\r\nFor more details:\r\nhttps://github.com/BitLucid/ninjawars/releases/tag/v1.8.12	2016-01-18 16:22:30.368131-05	2016-01-18 16:22:30.368131-05	release, pretty-urls, front-controller, readability
7	Release v1.8.14: Password Reset & Skill Fixes	Npcs: When NPCs v2 is released, some npcs like merchants will be "rich".\r\nKampo got a minor fix.\r\nFirebolt was broken, now is fixed.\r\nHeal was reporting results badly, now fixed.\r\nHarmonize was erroring out, now fixed.\r\nLots of pretty urls made available:\r\n/shrine /map /shop /doshin /inventory /clan /enemies /dojo, (not 100% of urls though)\r\nShrine behavior with dim mak and class change fixed up, bad monks, bad!\r\nPassword reset system in play!  /account_issues.php to use it.\r\n\r\n\r\nChanged for developers:\r\nmake ci, make all, make ci-test, make install, make db now exist.\r\nWork towards quests!\r\nLogging of emails to ./deploy/resources/email.log instead of dumping them out to page.\r\nVarious www scripts controllerified.\r\n(note that this release is slightly behind master currently)	2016-02-19 15:02:39.969089-05	2016-02-19 15:02:39.969089-05	skills, password_reset
8	Release v1.8.18: Item pretty urls, fix dim mak buying, fix poison touch.	Items:\r\nCan ninjawars.net/item/self_use/amanita or ninjawars.net/item/use/shuriken/tchalvak\r\nFix bug with buying dim Mak.\r\nFix bug with poison touch.\r\nFix bug when viewing player profile while logged out\r\nFix chat time agos.	2016-02-28 11:56:04.303576-05	2016-02-28 11:56:04.303576-05	release, bugfixes, to, poison touch, firebolt, dim mak,
9	Release v1.8.18: Item pretty urls, fix dim mak buying, fix poison touch.	Items:\r\nCan ninjawars.net/item/self_use/amanita or ninjawars.net/item/use/shuriken/tchalvak\r\nFix bug with buying dim Mak.\r\nFix bug with poison touch.\r\nFix bug when viewing player profile while logged out\r\nFix chat time agos.	2016-02-28 12:51:33.561424-05	2016-02-28 12:51:33.561424-05	release, bugfixes, to, poison touch, firebolt, dim mak,
10	Release v1.8.19: Hotfix release to fix DimMak buying.	This release only changes/fixes dojo so that DimMak is buyable again.	2016-03-09 09:42:27.649808-05	2016-03-09 09:42:27.649808-05	dimmak,hotfix
11	Release v1.8.21: Fixed Dojo for DimMak, fixed rankings, Kampo usable even if stealthed.	This will probably be the last feature-based release before a new major version number (v1.9.0), though there may be some more bugfix/hotfix minor releases.\r\n\r\nPlayer-visible changes:\r\nKampo usable while stealthed.\r\nFixed Dojo so you can buy DimMak again.\r\nFixed rankings numeric counting.\r\n\r\nhttps://github.com/BitLucid/ninjawars/releases/tag/v1.8.21	2016-03-09 10:31:40.243948-05	2016-03-09 10:31:40.243948-05	fixes,dojo,kampo,minor
12	Release v1.8.24: Item icons, ninja difficulty ratings, & bounty.	Item icons, ninja difficulty ratings, & bounty.\r\n\r\nChanges for players:\r\nKill points: How much you gain is based on difficulty rating, otherwise similar.\r\nFixed sight not displaying class.\r\nBounty now works off of your difficulty rating, not your level.\r\nBounty is capped again at 5,000石.\r\n32bit Images for Sushi, Phosphor Powder, Kunai, and more.\r\nShop & other gold areas: Change dollar sign to koku symbol.\r\nCombat: Blood splatter when/if you die in combat.\r\nUse button moved to the more logical place under items, give button put above items.\r\n\r\nAccount page options are still broken, sorry, they're on the list to fix.\r\n\r\n\r\n\r\n\r\nChanges for devs:\r\nSkills: Controllerified.\r\nDeity: Objectified.\r\nCONFIG: File now read by python tests.\r\nAccounts: Refactored.\r\nSignup: Nearly 100% code coverage.\r\nJS: Unit tested & lint/hinted.	2016-03-24 22:14:38.949998-04	2016-03-24 22:14:38.949998-04	items,images,difficulty rating,bounty,gold,combat
13	Hotfixes: Fix amanita use attempts erroring when no turns, viewing multiple pages of messages	Trying to use an amanita mushroom when you have no turns no longer errors.\r\n\r\nWhen you have multiple pages of messages, you can move from page to page without error again.	2016-03-25 12:54:58.518915-04	2016-03-25 12:54:58.518915-04	hotfix
14	Hotfix: Fixed password changing, email changing and account deletion on the account page.	Email changing, password changing, and account deletion from the account page were broken, but they're fixed now.	2016-03-28 21:37:45.938777-04	2016-03-28 21:37:45.938777-04	account
15	Login Lasts Longer	Just updated the login limiter to last much longer than the 24 hours it was set to before!\r\n\r\nShould be a convenient change.	2016-04-20 18:09:49.849678-04	2016-04-20 18:09:49.849678-04	login,config
281	Trying to test news again.	Try to test news again.	2018-07-21 11:25:45.460379-04	2018-07-21 11:25:45.460379-04	Test news,again
16	Stalk Skill, Stats, skill damage, and hitpoints rebalanced!	Overall summary:\r\n\r\nRaise your stamina? It raises your max hp.  Raise your strength?  Increases your damage (as before).  Ignore speed for now.\r\nStealth changed!  (decreases strength, increases stamina!)\r\nStalk skill!  (increased strength at the expense of stamina and speed)\r\n10 - 20 ninjas revive every minute (only if everyone dead).  Rankings get recalculated every minute.\r\nLots of rebalancing!\r\n\r\n\r\nNew Features:\r\n8a68c10 Stalk: implemented, cannibalizes stats,\r\n612546a Player: Stats now affected by STEALTH\r\ncb3defa Shop: Expensive items greyed out,\r\ndb3452d Npcs: Coloration for damage categories.\r\n56c07eb Deity: Revive 20 chars every minute, ranking recalculated every minute\r\n814a99d Shrine: Heal up to max dynamically, even if you have a higher (OR LOWER) stamina than your base.\r\n\r\nGameplay Rebalancing:\r\na9f7eeb Communism: Redux, dynamic str/sta/spe stat generation.\r\n(also Stealth effect rebalanced)\r\n8ae9900 Skills: Firebolt, harmonize, poison touch rebalanced.\r\n57a5d45 Player: Current max health based on current stamina.\r\n\r\n\r\nNotable Fixes:\r\n186f342 Show events with no sender!  Fix for longtime bug, expect no-sender events to show up!.\r\n0915891 Sight: Get dynamic str,spd,sta. Fixes discrepancies when sighting someone.\r\ne1996cc Intro: Fix class listings, remove reference to grey ninja\r\n69f446c Fix clan leaving 404, leaving clans should work again.\r\n9010300 Account: changing email fixed. (#717)\r\n90f2a4b Login: Fix /assistance link in login page.\r\n\r\nContent:\r\n0f6de8b Npcs: Spruce up npc list, add images for spider, firefly, and fireflies.\r\na6915a2 Deity: Stop deleting old personal messages, they'll stay forever for now.\r\nd9c5910 Event: Delete old events after 31 days instead of 4.\r\n4dcb891 Items: Add icon for Tessen Fan.\r\ne2b012f Signup: Made form elements much bigger,\r\n4a3e377 Font: Incorporated font-awesome vector font icons, they'll be crisper and cleaner.\r\n1928639 Index: Ninja-dropdown menu goes away after you leave it.\r\n\r\nDeveloper Changes: Deployment Notable Changes:\r\nb2210b6 Deity & TickController: Rearrange timing and behaviors.\r\n2613218 Deity: Rename deity scripts,\r\nce5475c Use StreamedViewResponse in Controllers\r\na76eb4c Characters: Account able to get multiple characters\r\n80757c9 Ratchets: Limit only 1 php file in www!	2016-05-14 09:50:10.154823-04	2016-05-14 09:50:10.154823-04	stalk,stats,stamina,revives,new features
17	Hiatus!	Hi All!\r\n\r\nDue to getting a new job (with http://get.tattleapp.com/ ) additions to ninjawars are on hiatus for a time.  This is bad timing because there're some bugs with regeneration and resurrection that really need fixing right away.\r\n\r\nI will work on those as soon as I can, but may have to solve them totally manually in the meantime.\r\n\r\nCatch you later,\r\n--Tchalvak/Roy	2016-06-30 11:44:43.034731-04	2016-06-30 11:44:43.034731-04	time, releases, delays, resurrection
18	New top menu bar for the game.	We did a lot of work over quite a while, on the top bar of the site, to simplify it, make it more modern and cleaner.\r\n\r\nIn addition, we've given the site more space for things like the ninja list and combat, npcs, etc.\r\n\r\nLess clutter, more space!	2016-10-22 16:39:11.567404-04	2016-10-22 16:39:11.567404-04	bootstrap top bar, layout, links
19	Top Health Bar!	You now get a redish health-bar at the top of the page showing your current health percent.  For now, 0% is just a tiny bar, I may accent when you're dead in the future.	2017-11-30 18:03:32.828012-05	2017-11-30 18:03:32.828012-05	health,combat
56	sadfsadfsad	M	2018-07-19 01:43:03.148659-04	2018-07-19 01:43:03.148659-04	sdf asdf sadf s,asdlfkjasdlkfjasdf
57	Yezz, news working again.	Aw yeah...	2018-07-19 01:43:22.784728-04	2018-07-19 01:43:22.784728-04	News working again?
\.


--
-- Data for Name: account_news; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.account_news (_account_id, _news_id, created_date) FROM stdin;
5442	1	2015-09-18 22:20:26.108667-04
5442	2	2015-10-01 14:43:16.663617-04
5442	3	2015-10-21 10:45:01.457241-04
5442	4	2015-11-29 01:34:53.349363-05
5442	5	2015-12-26 12:43:12.436566-05
5442	6	2016-01-18 16:22:30.368131-05
5442	7	2016-02-19 15:02:39.969089-05
5442	8	2016-02-28 11:56:04.303576-05
5442	9	2016-02-28 12:51:33.561424-05
5442	10	2016-03-09 09:42:27.649808-05
5442	11	2016-03-09 10:31:40.243948-05
5442	12	2016-03-24 22:14:38.949998-04
5442	13	2016-03-25 12:54:58.518915-04
5442	14	2016-03-28 21:37:45.938777-04
5442	15	2016-04-20 18:09:49.849678-04
5442	16	2016-05-14 09:50:10.154823-04
5442	17	2016-06-30 11:44:43.034731-04
5442	18	2016-10-22 16:39:11.567404-04
5442	19	2017-11-30 18:03:32.828012-05
5442	281	2018-07-21 11:25:45.461965-04
5442	56	2018-07-19 01:43:03.150956-04
5442	57	2018-07-19 01:43:22.786245-04
\.


--
-- Data for Name: class; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.class (class_id, class_name, class_active, class_note, class_tier, class_desc, class_icon, theme, identity) FROM stdin;
1	Viper	t	Poison	1	\N	\N	Black	viper
2	Crane	t	Speed	1	\N	\N	Blue	crane
4	Dragon	t	Healing	1	\N	\N	White	dragon
3	Tiger	t	Strength	1	\N	\N	Red	tiger
5	Mantis	f	Smoke	1	\N	\N	Gray	mantis
\.


--
-- Data for Name: players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.players (player_id, uname, health, strength, gold, messages, kills, turns, verification_number, active, email, level, status, member, days, ip, bounty, created_date, resurrection_time, last_started_attack, energy, avatar_type, _class_id, ki, stamina, speed, karma, kills_gained, kills_used, description, instincts, beliefs, goals, traits) FROM stdin;
134853	Beagle	0	45	0	Kill me, I'm an admin.	37	300	640536	1		9	0	0	0	4.15.211.27	0	2010-02-18 09:20:17.319739-05	18	2015-10-28 23:37:23.85619-04	1000	1	2	59	45	45	10	175	140					
128274	Tchalvak	230	100	1301	Contact me via the staff page, or use the official email, ninjawarslivebythesword@gmail.com\r\n\r\nDang Bat Crazy Devil	507	8893	3259	1		20	0	0	0	69.207.179.166	1462	2009-11-03 07:04:53.223615-05	12	2016-03-24 23:35:19.798229-04	1000	1	1	928	100	100	33	126	105	is swathed in a simple brown shirt and hakama, his slicked down hair tied in a knot at the back of his head.  His green eyes contain a hint of something behind them, but he carries no metal, only a simple twisted walking stick.	My instinct is to throw a smoke bomb and vanish at the first sign of a fight.			fast,suave,handsome,dark
\.


--
-- Data for Name: account_players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.account_players (_account_id, _player_id, last_login, created_date) FROM stdin;
5442	128274	2010-08-02 18:38:39.874268-04	2009-11-03 07:04:53.223615-05
2948	134853	2010-08-02 18:38:39.874268-04	2010-02-18 09:20:17.319739-05
\.


--
-- Data for Name: chat; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.chat (chat_id, sender_id, message, date) FROM stdin;
\.


--
-- Data for Name: clan; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.clan (clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description) FROM stdin;
1	clan_fixture_test1	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
112	clan_fixture_test112	2010-09-24 19:21:16.400153-04	Tchalvak	\N	fixtures_test
108	clan_fixture_test108	2010-09-13 02:42:09.402398-04	Tchalvak	\N	fixtures_test
77	clan_fixture_test77	2010-08-10 21:12:05.260692-04	Tchalvak		fixtures_test
131	clan_fixture_test131	2010-10-15 20:55:59.411072-04	Tchalvak	\N	fixtures_test
94	clan_fixture_test94	2010-08-19 06:26:26.545643-04	Tchalvak	\N	fixtures_test
193	clan_fixture_test193	2011-10-03 17:48:44.429139-04	Tchalvak	\N	fixtures_test
168	clan_fixture_test168	2011-04-11 10:17:25.810824-04	Tchalvak	\N	fixtures_test
75	clan_fixture_test75	2010-08-09 20:37:38.655621-04	Tchalvak	\N	fixtures_test
133	clan_fixture_test133	2010-11-07 16:25:33.436599-05	Tchalvak	\N	fixtures_test
135	clan_fixture_test135	2010-11-25 13:53:05.393573-05	Tchalvak	\N	fixtures_test
102	clan_fixture_test102	2010-09-11 06:26:28.840892-04	Tchalvak		fixtures_test
104	clan_fixture_test104	2010-09-11 12:59:30.916074-04	Tchalvak	\N	fixtures_test
117	clan_fixture_test117	2010-09-29 23:24:19.155782-04	Tchalvak	\N	fixtures_test
20	clan_fixture_test20	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
27	clan_fixture_test27	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
29	clan_fixture_test29	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
36	clan_fixture_test36	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
139	clan_fixture_test139	2010-12-14 15:47:01.08422-05	Tchalvak	\N	fixtures_test
39	clan_fixture_test39	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
40	clan_fixture_test40	2010-03-25 13:10:09.010102-04	Tchalvak	\N	fixtures_test
169	clan_fixture_test169	2011-04-19 14:18:59.416078-04	Tchalvak	http://img847.imageshack.us/img847/9133/830pxvariaflag.png	fixtures_test
47	clan_fixture_test47	2010-05-16 01:45:08.614659-04	Tchalvak	\N	fixtures_test
45	clan_fixture_test45	2010-04-29 05:10:54.064977-04	Tchalvak	\N	fixtures_test
232	clan_fixture_test232	2013-06-04 02:39:09.399985-04	Tchalvak		fixtures_test
228	clan_fixture_test228	2013-03-09 11:21:00.62408-05	Tchalvak	\N	fixtures_test
195	clan_fixture_test195	2011-10-30 06:43:52.477042-04	Tchalvak		fixtures_test
237	clan_fixture_test237	2014-09-12 21:07:33.002409-04	Tchalvak	\N	fixtures_test
238	clan_fixture_test238	2015-02-21 10:40:23.872798-05	Tchalvak	\N	fixtures_test
213	clan_fixture_test213	2012-06-14 14:27:24.796132-04	Tchalvak		fixtures_test
207	clan_fixture_test207	2012-03-23 17:49:53.801076-04	Tchalvak		fixtures_test
230	clan_fixture_test230	2013-04-29 12:15:16.04129-04	Tchalvak	\N	fixtures_test
183	clan_fixture_test183	2011-08-19 15:42:59.50721-04	Tchalvak	\N	fixtures_test
220	clan_fixture_test220	2012-09-01 21:34:11.053294-04	Tchalvak	\N	fixtures_test
41	clan_fixture_test41	2010-03-25 13:10:09.010102-04	Tchalvak		fixtures_test
190	clan_fixture_test190	2011-09-24 23:33:43.047736-04	Tchalvak	\N	fixtures_test
223	clan_fixture_test223	2012-10-04 09:24:50.19241-04	Tchalvak	\N	fixtures_test
248	clan_fixture_test248	2015-09-17 13:26:18.591168-04	Tchalvak	\N	fixtures_test
243	clan_fixture_test243	2015-07-17 13:41:51.927762-04	Tchalvak		fixtures_test
245	clan_fixture_test245	2015-08-03 17:14:45.596097-04	Tchalvak		fixtures_test
606	clan_fixture_test606	2018-07-21 13:07:58.101141-04	Tchalvak	\N	fixtures_test
\.


--
-- Data for Name: clan_player; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.clan_player (_clan_id, _player_id, member_level, created_at) FROM stdin;
606	128274	2	2018-07-21 13:07:58.104121-04
\.


--
-- Data for Name: skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.skill (skill_id, skill_level, skill_is_active, skill_display_name, skill_internal_name, skill_type) FROM stdin;
1	1	t	Ice Bolt	ice	targeted
2	6	t	Cold Steal	coldsteal	targeted
3	1	t	Speed	speed	passive
4	1	t	Chi	chi	passive
6	1	t	Fire Bolt	fire	targeted
7	1	t	Blaze	blaze	combat
8	2	t	Deflect	deflect	combat
9	1	t	Poison Touch	poison	targeted
10	1	t	Hidden Resurrect	stealthres	passive
11	1	t	Sight	sight	targeted
12	1	t	Stealth	stealth	self-only
13	1	t	Unstealth	unstealth	self-only
14	2	t	Steal	steal	targeted
15	2	t	Kampo	kampo	self-only
16	2	t	Evasion	evasion	combat
5	20	t	Midnight Heal	midnightheal	passive
17	1	t	Heal	heal	targeted
\.


--
-- Data for Name: class_skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.class_skill (_class_id, _skill_id, class_skill_level) FROM stdin;
1	9	\N
1	10	\N
2	1	\N
2	3	\N
4	4	\N
3	6	\N
3	7	\N
4	17	\N
2	15	\N
4	16	\N
\.


--
-- Data for Name: dueling_log; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.dueling_log (id, attacker, defender, won, killpoints, date) FROM stdin;
\.


--
-- Data for Name: effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.effects (effect_id, effect_identity, effect_name, effect_verb, effect_self) FROM stdin;
1	wound	Wound	Wounds	f
2	fire	Fire	Burns	f
3	ice	Ice	Freezes	f
4	shock	Shock	Shocks	f
5	acid	Acid	Dissolves	f
6	void	Void	Taints	f
7	flare	Flare	Blinds	f
8	poison	Poison	Poisons	f
9	paralysis	Paralysis	Paralyzes	f
10	slice	Slice	Slices	f
11	bash	Bash	Bashes	f
12	pierce	Pierce	Pierces	f
13	slow	Slow	Slows down	f
14	speed	Speed	Speeds up	t
15	stealth	Stealthed	Hides	t
16	vigor	Vigor	Energizes	t
17	strength	Strength	Strengthens	t
18	weaken	Weaken	Weakens	f
19	heal	Heal	Heals	t
20	healing	Healing	Healed	t
21	regen	Regenerate	Regenerating	t
22	death	Death	Dying	f
\.


--
-- Data for Name: enemies; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.enemies (_player_id, _enemy_id) FROM stdin;
\.


--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.events (event_id, send_to, send_from, message, unread, date) FROM stdin;
\.


--
-- Data for Name: flags; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.flags (flag_id, flag, flag_type, created_at) FROM stdin;
1	bugabuse	2	2016-02-28 16:55:20.337245-05
2	multiplaying	3	2016-02-28 16:55:20.337245-05
3	spamming	4	2016-02-28 16:55:20.337245-05
4	paused	10	2016-02-28 16:55:20.337245-05
5	moderator	21	2016-02-28 16:55:20.337245-05
6	bugfinder	22	2016-02-28 16:55:20.337245-05
\.


--
-- Data for Name: inventory; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.inventory (item_id, amount, owner, item_type) FROM stdin;
\.


--
-- Data for Name: item; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.item (item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, plural, other_usable, traits) FROM stdin;
14	lantern	Hooded Lantern	50	\N	f	A lantern for light and flame	t	t	1	20	\N	t	\N	t	
5	shuriken	Shuriken	50	mini_star.png	t	Reduces health	f	f	1	\N	\N	f	\N	t	
3	amanita	Amanita Mushroom	225	mushroom.png	t	Increases Turns	t	t	1	\N	6	t	s	t	
2	caltrops	Caltrops	125	caltrops.png	t	Reduces Turns	f	f	1	\N	-6	f	\N	t	
6	dimmak	Dim Mak	1000	scroll.png	f	\N	t	t	1	\N	\N	f	\N	t	
9	charcoal	Charcoal	10	\N	f	Purges Poisons, Burns Merrily	t	t	1	20	\N	f	\N	f	
12	shell	Shell Fragment	700	\N	f	Insulates against Flame	t	t	1	0	\N	f	\N	f	
13	prayerwheel	Prayer Wheel	150	\N	f	Lifts Curses	t	t	1	0	\N	f	\N	f	
15	sushi	Sushi	50	\N	f	For immediate consumption	t	t	1	20	\N	f	\N	f	
16	fugu	Fugu Blowfish	50	\N	f	Delicious, or Deadly	t	t	1	20	\N	f	\N	f	
17	oyoroi	O-yoroi Great Armor	3000	\N	f	Woven Armor with a Metal Breastplate against piercing and slashing	t	t	1	3000	\N	f	s	f	
18	kozando	Kozan-do Scale Armor	1600	\N	f	Scale Plated Armor against slashing	t	t	1	0	\N	f	s	f	
19	domaru	Do-Maru Woven Armor	1000	\N	f	Woven Armor against piercing and slashing	t	t	1	0	\N	f	s	f	
20	tanko	Tanko Scale Armor	900	\N	f	Lamellate Armor against Crushing Blows	t	t	1	0	\N	f	s	f	
21	tatamido	Tatami-do Folding Armor	1500	\N	f	Laced Squares of Flexible Leather for easy movement	t	t	1	0	\N	f	s	f	
22	keikogi	Keiko-Gi Suit	70	\N	f	Thick Cloth Uniform for unfettered movement	t	t	1	0	\N	f	s	f	
24	hakama	Hakama Garb	30	\N	f	Pleated, Loose Pants and Shirt for unfettered movement	t	t	1	0	\N	f	s	f	
25	mask	Menpo Mask	600	\N	f	For Disguise or Intimidation	t	t	1	0	\N	f	s	f	
27	meito	Meito Named Katana	3000	\N	f	Folded-Steel Named Sword for Slashing	t	f	1	3000	\N	f	\N	f	
28	naginata	Naginata Spear	750	\N	f	Long Reached, Curved Spear for Piercing and Slashing	f	f	1	750	\N	f	s	f	
30	kusarigama	Kusarigama Chain Sickle	500	\N	f	For Swinging Slashes and Entanglement	t	f	1	500	\N	f	s	f	
32	tetsubo	Tetsubo Club	140	\N	f	For Piercing, Crushing Blows	f	f	1	140	\N	f	\N	f	
33	nunchaku	Nunchaku	180	\N	f	Thrashing Blows with a long reach	t	f	1	180	\N	f	\N	f	
34	zanbato	Zanbato Long Sword	660	\N	f	For Heavy Slashing Blows with a long reach	t	f	1	660	\N	f	\N	f	
35	eku	Eku Wooden Oar	30	\N	f	For Slow, Wide-Arcing Blows	t	f	1	130	\N	f	s	f	
36	ono	Ono Axe	10	\N	f	Great for Beheadings and De-limbing	f	f	1	110	\N	f	s	f	
37	nekote	Neko-Te Claws	450	\N	f	For Poisoned slashing or climbing	f	t	1	30	\N	f	\N	f	
23	kimono	Kimono	170	\N	f	Light Silk Clothing for formal wear	t	t	1	0	\N	f	s	f	
26	katana	Katana	1800	\N	f	Crafted Sword for Slashing	f	f	1	1800	\N	f	\N	f	
38	hamagari	Hamagari Saw	77	\N	f	For Poisoned slashing or climbing	f	t	1	177	\N	f	s	f	
39	bo	Bo Staff	70	\N	f	For Ease of Walking	t	t	1	170	\N	f	s	f	
31	kama	Kama Sickle	55	\N	f	For Reaping Rice	f	f	1	55	\N	f	\N	f	
7	ginsengroot	Ginseng Root	1000	ginseng_root.png	f	\N	t	t	1	\N	\N	t	s	t	
8	tigersalve	Tiger Salve	3000	tiger_salve.png	f	\N	t	t	1	\N	\N	t	s	t	
4	smokebomb	Smoke Bomb	150	smoke_bomb.png	t	Stealths a Ninja	f	f	1	\N	\N	t	s	t	
10	sake	Sake	30	sake.png	f	Warms the Soul	t	t	1	1	\N	f	\N	f	
11	mirror	Mirror Shard	120	mirror_shard.png	f	Reflects Light	t	t	1	5	\N	f	\N	f	
29	kunai	Kunai	50	kunai.png	t	For Digging and Planting	t	t	1	50	\N	f	\N	t	
1	phosphor	Phosphor Powder	175	phosphor_powder.png	t	Burns fiercely	f	f	1	\N	\N	f	s	t	
40	tessen	Tessen Fan	150	tessen.png	t	For Cooling Air	f	t	1	40	\N	f	s	t	
\.


--
-- Data for Name: item_effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.item_effects (_item_id, _effect_id) FROM stdin;
1	2
1	7
1	1
2	13
2	12
2	1
7	16
8	17
3	14
6	22
6	1
5	10
5	1
4	15
29	1
29	12
40	1
\.


--
-- Data for Name: levelling_log; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.levelling_log (id, killpoints, levelling, killsdate, _player_id) FROM stdin;
\.


--
-- Data for Name: login_attempts; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.login_attempts (attempt_id, username, ua_string, ip, successful, additional_info, attempt_date) FROM stdin;
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.messages (message_id, message, date, send_to, send_from, unread, type) FROM stdin;
\.


--
-- Data for Name: password_reset_requests; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.password_reset_requests (request_id, _account_id, nonce, created_at, used, updated_at) FROM stdin;
\.


--
-- Data for Name: past_stats; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.past_stats (id, stat_type, stat_result) FROM stdin;
2	Most Kills Last Month	0
3	Total Kills Last Month	0
6	Total Kills Yesterday	0
1	Most Kills Yesterday	0
5	Previous Month's Vicious Killer	Tchalvak
4	Yesterday's Vicious Killer	Tchalvak
\.


--
-- Data for Name: player_rank; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.player_rank (rank_id, _player_id, score) FROM stdin;
\.


--
-- Data for Name: players_flagged; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.players_flagged (players_flagged_id, player_id, flag_id, "timestamp", originating_page, extra_notes) FROM stdin;
\.


--
-- Data for Name: quests; Type: TABLE DATA; Schema: public; Owner: kzqai
--

COPY public.quests (quest_id, title, _player_id, description, tags, karma, rewards, obstacles, proof, created_at, updated_at, expires_at, type, difficulty) FROM stdin;
\.


--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public.settings (setting_id, player_id, settings_store) FROM stdin;
\.


--
-- Data for Name: time; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY public."time" (time_id, time_label, amount) FROM stdin;
1	hours	4
\.


--
-- Name: accounts_account_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.accounts_account_id_seq', 33552, true);


--
-- Name: chat_chat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.chat_chat_id_seq', 1, false);


--
-- Name: chat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.chat_id_seq', 485905, true);


--
-- Name: clan_clan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.clan_clan_id_seq', 606, true);


--
-- Name: class_class_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.class_class_id_seq', 6, true);


--
-- Name: dueling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.dueling_log_id_seq', 1291910, true);


--
-- Name: effects_effect_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.effects_effect_id_seq', 22, true);


--
-- Name: events_event_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.events_event_id_seq', 1, false);


--
-- Name: flags_flag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.flags_flag_id_seq', 1, false);


--
-- Name: inventory_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.inventory_item_id_seq', 50680, true);


--
-- Name: item_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.item_item_id_seq', 40, true);


--
-- Name: levelling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.levelling_log_id_seq', 4471970, true);


--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.login_attempts_attempt_id_seq', 1, false);


--
-- Name: messages_message_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.messages_message_id_seq', 1, false);


--
-- Name: news_news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.news_news_id_seq', 314, true);


--
-- Name: password_reset_requests_request_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.password_reset_requests_request_id_seq', 1, false);


--
-- Name: past_stats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.past_stats_id_seq', 1, false);


--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.player_rank_rank_id_seq', 2, true);


--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.players_flagged_players_flagged_id_seq', 1, false);


--
-- Name: players_player_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.players_player_id_seq', 175102, true);


--
-- Name: quests_quest_id_seq; Type: SEQUENCE SET; Schema: public; Owner: kzqai
--

SELECT pg_catalog.setval('public.quests_quest_id_seq', 1, false);


--
-- Name: settings_setting_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.settings_setting_id_seq', 1, false);


--
-- Name: skill_skill_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.skill_skill_id_seq', 16, true);


--
-- Name: time_time_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('public.time_time_id_seq', 1, true);


--
-- PostgreSQL database dump complete
--

