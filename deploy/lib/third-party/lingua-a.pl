use Lingua::EN::Inflect qw ( PL PL_N PL_V PL_ADJ NO NUM
                              PL_eq PL_N_eq PL_V_eq PL_ADJ_eq
                              A AN
                              PART_PRES
                              ORD NUMWORDS
                              inflect classical
                              def_noun def_verb def_adj def_a def_an ); 

print A($ARGV[0]);
