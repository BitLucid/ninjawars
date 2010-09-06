-- These are somewhat like statuses, but they occur in an active, ongoing context, e.g.
-- The Item <description> Bob.
insert into effects values 
    (default, 'fireresistance', 'Fire-Resistance', 'Cools', TRUE),
    (default, 'insulation', 'Insulated', 'Insulates', TRUE),
    (default, 'ground', 'Grounded', 'Grounds', TRUE),
    (default, 'bless', 'Blessed', 'Blesses', TRUE),
    (default, 'poisonresistance', 'Immunized', 'Immunizes', TRUE),
    (default, 'acidresistance', 'Acid Protected', 'Neutralizes', TRUE)
;

--    (default, 'speed', 'Speed', 'Speeds up', TRUE),
--    (default, 'stealth', 'Stealthed', 'Hides', TRUE),
--    (default, 'vigor', 'Vigor', 'Energizes', TRUE),

