CREATE UNIQUE INDEX CONCURRENTLY unique_item_counts
ON inventory (owner, item_id);
ALTER TABLE inventory
ADD CONSTRAINT unique_item_counts
UNIQUE USING INDEX unique_item_counts;