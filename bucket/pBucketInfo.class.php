<?

class pBucketInfo {
    
    
    private $b;
    private $bucket_items;
    private $full_items = array();
    private $db;
    private $classID;
    
    private $fName;
    private $fPrice;
    
    public function __construct(pBucket $bucket,$classID,$field_map = array('name'=>'Name','price'=>'Price')) {
        global $nc_core;
        
        $this->b = $bucket;
        $this->bucket_items = $bucket->get();
        $this->db = $nc_core->db;
        
        $this->classID = $classID;
        
        $this->fName = $field_map['name'];
        $this->fPrice = $field_map['price'];
        
        
    }
    
    /**
    * 
    */ 
    public function items() {
        if (!empty($this->bucket_items))    {
             $ids = $this->db->get_results("SELECT Message_ID,".$this->fName.",".$this->fPrice." FROM Message".$this->classID." WHERE Message_ID IN (".implode(',',array_keys($this->bucket_items) ).")",ARRAY_A);
             foreach ($ids as $el) {
                $item_total_sum  =   $el[$this->fPrice] * $this->bucket_items[$el['Message_ID']];
                
                $this->full_items[] = array(
                                            'id'=> $el['Message_ID']
                                            ,'price'=> $el[$this->fPrice]
                                            ,'name'=> $el[$this->fName]
                                            ,'count'=> $this->bucket_items[$el['Message_ID']]
                                            ,'total_sum'=> $item_total_sum
                                            ,'link'=> nc_message_link ($el['Message_ID'],$this->classID)
                                            );
             }
        }
        
        return $this->full_items;
    }
    
    public function total_sum() {
        $total = 0;
        
        if (empty($this->full_items)) {$this->items();}
        
        if (!empty($this->full_items)) {
            foreach ($this->full_items as $item)  {
                $total += $item['total_sum'];
            }
        }
        return $total;
    }
}