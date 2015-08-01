<?
namespace \simple\module\Remote;

class Connection
{
	private $host;

	private $curl;

	public function __construct($host)
	{
		$host = trim($host);
		if(empty($host))
			throw new \InvalidArgumentException("Error host");
		$this->host = $host;
	}

	public function send($url)
	{
		return 'some content';
	}
}
?>