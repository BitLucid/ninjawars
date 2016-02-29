alter table player_rank alter column rank_id set default nextval('player_rank_rank_id_seq'::regclass);
drop sequence player_rank_rank_id_seq1;
