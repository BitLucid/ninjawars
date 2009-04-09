#!/usr/bin/ruby -s
#
# This utility uses rsync to deploy files to multiple servers.
#
# With rsync, trailing slashes on sources are significant.
# See: http://www.mikerubel.org/computers/rsync_snapshots/#Rsync
#
# We normalize paths so that they always look like one of these:
#     rsync dir/ host:dir
#     rsync dir/file host:dir/file
#

require 'yaml'
require 'pathname'

conf = YAML::load(File.open('shotgun.conf'))

if ARGV.length < 1 or ($path and !$src)
    STDERR.puts \
        "Usage: shotgun.rb [-src=SRC [-path=PATH]] DEST [DEST]...\n\n" +
        "  PATH is a directory or file appended to source and DESTs\n" +
        "  SRC can be one of the following (all used by default):\n" +
        conf['sources'].sort.map{|src|"    #{src}\n"}.join +
        "  DEST can be:\n" +
        "    all\n" +
        conf['dests'].keys.sort.map{|dest|"    #{dest}\n"}.join
    exit!
end

rsync = conf['rsync'] + conf['exclude'].map{|path|" --exclude=#{path}"}.join
path = $path.instance_of?(String) ? $path.gsub(/\/$/, "") : ""
sources = $src.instance_of?(String) ? [$src] : conf['sources']
dests = ARGV[0] == "all" ? conf['dests'].keys : ARGV[0..-1]

bad_dests = dests.reject{|dest| conf['dests'].has_key?(dest)}
if bad_dests.length > 0
    STDERR.puts "Invalid dests: " + bad_dests.join(", ")
    exit!
end

sources.each do |src|
    src = src.gsub(/\/$/, "") + "/" # trailing slash is important!
    
    dest_dir = conf['dest_dirs'][src] || ""
    
    src += path
    src += "/" if path.length > 0 and File.directory?(src)
    
    next if !File.exists?(src)
    
    puts ("_" * 79) + "\nSource: #{src}\n"
    
    dests.each do |dest|
        dest = conf['dests'][dest]
        dest += ("/" + dest_dir) if dest_dir.length > 0
        dest = dest.gsub(/\/$/, "") + (path.length > 0 ? "/" + path : "")
        dest = Pathname.new(dest).cleanpath
        puts "\n#{dest}"
        puts `#{rsync} #{src} #{dest}`.split("\n")[1..-2].map{|ln|"  #{ln}"}
    end    
end
