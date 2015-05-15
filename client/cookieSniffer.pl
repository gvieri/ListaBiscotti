use Net::Pcap;
use NetPacket::Ethernet qw(:types); 
use NetPacket::IP qw(:protos); 
use NetPacket::ICMP ;
use NetPacket::TCP ;
use NetPacket::UDP ;
use Getopt::Std;

use strict;


my $count=0;
my $count_TCP=0 ;
my $count_ICMP=0 ;
my $count_UDP=0;
my @seqArray = () ; 
my $snaplen=1024; 
my $promisc=1;
my $to_ms=0; 
my %options=();
my $err;
my $dev;
my $cycle;

getopts("d:p:",\%options);

$dev="eth0";
$cycle= 1000 ;
if (defined ($options{d}) ) { $dev=$options{d}; } 
if (defined ($options{p}) ) { $cycle=$options{p}; }

print "=============\ndevice\t$dev\ncycle\t$cycle\n=============\n"; 

my ($address, $netmask, $err);
Net::Pcap::lookupnet($dev, \$address, \$netmask, \$err);
### must be modified 
print STDOUT "$dev: addr/mask -> $address/$netmask\n";



my $object = Net::Pcap::open_live($dev, $snaplen, $promisc, $to_ms, \$err ) ; 
pcap_loop($object, $cycle, \&process_packet, "just for the demo");

pcap_close($object);

sub process_packet {
        my ($user_data, $header, $packet) = @_;
	$count ++ ; 

	my $eth = NetPacket::Ethernet->decode($packet) ; 
	if ( $eth->{type}== ETH_TYPE_IP) { 

		my $ip=NetPacket::IP->decode($eth->{data}) ; 
		if($ip->{proto} == IP_PROTO_ICMP) { 
			$count_ICMP++;
		} elsif ($ip->{proto} == IP_PROTO_TCP) { 
			$count_TCP++;
			my $tcp=NetPacket::TCP->decode($ip->{data} ); 
			if ($tcp->{src_port} == 80 ) {
				my $flags 	= $tcp->{flags} ;
				my $seqnum	= $tcp->{seqnum}; 
				if ($flags == 16 ) { 
					my ($h,$dummy,$rawPayload,$payload); 
					$rawPayload=$tcp->{data};
					($payload,$dummy)=split (/\n\s/, $rawPayload); 
					my @h=split (/\n/,$payload); 
					foreach $h (@h) { 
						if ($h=~/^Set-Cookie/)  {
							print $h."\n"; 
						}
					}
				} 
			} elsif( $tcp->{dst_port} == 80 ) { 
# to be implemented 

			} else { 
				## not http protocols... 
			} 
		} if($ip->{proto} == IP_PROTO_UDP) { 
			$count_UDP++;
		} 
	}  
    }

print STDOUT "total packet = $count\n";


