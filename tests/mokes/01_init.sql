create table users (id serial primary key, name text, email text, created_at timestamptz default NOW());
