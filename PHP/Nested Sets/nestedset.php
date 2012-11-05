<?php

/**
 * Framework to get a flat list of items from a stored nested set, and vice versa
 *
 * @author Ben Freke
 */
class nestedset
{

    /**
     * Gets the full nested set of items
     * 
     * @todo Cache the output
     * @param Int $resourceId
     * @return JSON The json array of categories in this resource 
     */
    protected function getNodes($resourceId)
    {
        // Make sure we have a definited connection to our database
        $db = new DB();

        $sql = "
        SELECT 
            Child.resource_id as resourceId,
            Child.id as nodeId,              
            Child.lft, 
            Child.rgt, 
            Child.title, 
            Child.url, 
            (COUNT(Parent.title) - 1) AS depth
        FROM resource_nodes AS Parent,
            resource_nodes AS Child
            
        WHERE Child.lft BETWEEN Parent.lft AND Parent.rgt
            AND Child.resource_id = {$resourceId}
            AND Parent.resource_id = {$resourceId}
            
        GROUP BY Child.id
        ORDER BY Child.lft
        ";

        $data = $db->getObjects($sql);

// Now we need to create the adjacent list from this dataset
        $depth = array(); // Holds our parent items
        $nodes = array(); // Our final output

        foreach ($data as $key => $node) {
            // @todo: Update everything using the key
            // Leaf should be true if there are no children
            // It MUST be a boolean, not a string
            $data[$key]->leaf = ($node->rgt - 1 == $node->lft) ? true : false;
            $data[$key]->expanded = false;
// I have to set the very first item to the first depth
            if (empty($depth)) {
                $depth[0] = $node;
            }
            $data[$key]->parent = ($node->depth > 0) ? $depth[($node->depth - 1)] : 0;
            $data[$key]->children = array();
            $data[$key]->right = 0;
            $data[$key]->left = 0;
            if (isset($depth[$node->depth]->catId)
                    && isset($node->parent->catId)
                    && ($depth[$node->depth]->parent == $node->parent->catId)
            ) {
// Set my left and right values
// Set the left only when it is higher than my parents id
                $data[$key]->left = (($depth[$node->depth]->catId > $node->parent->catId)
                        && ($depth[$node->depth]->catId !== $node->catId) ) ? $depth[$node->depth]->catId : 0;
// Set the previous node's right!
                $depth[$node->depth]->right = $node->catId;
            }
            $depth[$node->depth] = $node; // So I know what my parent at this level is
// If I have a parent id, add this item as a child of it.
            if (!empty($node->parent)) {
                $data[$key]->parent->children[$node->catId] = $node;
// Make this an id instead of the entire object
                $data[$key]->parent = $node->parent->catId;
            } else {
// If not, make it a new item!
                $nodes[$node->catId] = $node;
            }
        }
        // You may not need this next step
        array_walk($nodes, array($this, 'reIndexArray'));
        return array_values($nodes);
    }

    /**
     * Recursively re-indexes the child elements of this node so extjs is happy
     * @param Object $input A category object
     */
    public function reIndexArray($input)
    {
        if (!empty($input->children)) {
            $input->children = array_values($input->children);
            array_walk($input->children, array($this, 'reIndexArray'));
        }
    }

    /**
     * Insert a new node
     * @param type $data
     */
    public function insertNode($data)
    {
        $resourceId = $data['resource'];
        // Make sure the page has a good name
        $title = $data['catName'];
        $data['title'] = $data['catName'];
        $data['urlFor'] = 0; // It's a category
        $url = $this->getURL($data);
        // If I need to update this with the correct seo_url later, title is false
        $updateSEO = false;
        if ($url === false) {
            $updateSEO = true;
            // I have to clean this here, as normally getURL does the cleaning
            $url = htmlentities(str_replace(' ', '_', strtolower($data['catName'])));
        }

        $table = new Snapp_Model('website_resources_categories');

        // Find out where this is going
        if (isset($data['catId'])) {
            // Inserting under a category at the top of the list
            $result = 'none';
            // Get the right hand side of my parent
            $sql = "SELECT rgt
                FROM website_resources_categories
                WHERE id = {$data['catId']}
                    AND website_resources_id = {$resourceId}";
            $result = $this->cms->db->getObject($sql);
            $rightValue = $result->rgt;

            // Push everything to the right that needs to be
            $sql = "UPDATE website_resources_categories
                SET rgt = rgt + 2
                WHERE rgt >= {$rightValue}
                    AND website_resources_id = {$resourceId}";
            $this->cms->db->query($sql);

            $sql = "UPDATE website_resources_categories
                SET lft = lft + 2
                WHERE lft > {$rightValue}
                    AND website_resources_id = {$resourceId}";
            $this->cms->db->query($sql);

            // Now insert
            $left = $rightValue;
            $right = $rightValue + 1;
        } else {
            $maxRight = 0;
            // Inserting as a root category
            // Now get the highest rgt values of this Resource
            $sql = "SELECT MAX(rgt) as maxRight
                FROM website_resources_categories
                WHERE website_resources_id = {$resourceId}";
            $result = $this->cms->db->getObject($sql);
            if (isset($result->maxRight)) {
                $maxRight = $result->maxRight;
            }
            $left = $maxRight + 1;
            $right = $maxRight + 2;
        }

        $rowValues = array(
            'title' => $title,
            'url' => $url,
            'lft' => $left,
            'rgt' => $right,
            'website_resources_id' => $resourceId
        );
        $result = $table->save($rowValues);
        $newCatId = $result->id;

        if ($updateSEO) {
            // I wasn't able to generate the SEO URL the first time
            $data['id'] = $newCatId;
            // All my needed values were set at the start
            $url = $this->getURL($data);
            $rowValues = array(
                'id' => $newCatId,
                'url' => $url
            );
            $result = $table->save($rowValues);
        }
        // Now include the admin user as able to see it by default
        $rowValues = array(
            'category_id' => $newCatId,
            'website_user_group_id' => 1
        );
        $table = new Snapp_Model('website_resources_categories_group_access');
        $result = $table->save($rowValues);

        header('Content-type: application/json');
        echo json_encode($result);
    }

    /**
     * Delete this node, and any subnodes
     * @param type $data
     */
    public function deleteNode($data)
    {
        $output = array();
        $resourceId = $data['resource'];
        $catId = $data['id'];
        // Get the left and right values of this category
        $sql = "SELECT lft, rgt
            FROM website_resources_categories
            WHERE id = {$catId}
                AND website_resources_id = {$resourceId}";
        $result = $this->cms->db->getObject($sql);
        $left = $result->lft;
        $right = $result->rgt;

        // Delete any items that are within these two values
        $sql = "DELETE FROM website_resources_categories
            WHERE lft >= {$left}
                AND rgt <= {$right}
                AND website_resources_id = {$resourceId}";
        $this->cms->db->query($sql);

        // Move everything that is right, left!
        $moveLeft = $right - $left + 1;
        $sql = "UPDATE website_resources_categories
            SET lft = lft - {$moveLeft}
            WHERE lft > {$left}
                AND website_resources_id = {$resourceId}";
        $result = $this->cms->db->query($sql);

        $sql = "UPDATE website_resources_categories
            SET rgt = rgt - {$moveLeft}
            WHERE rgt > {$right}
                AND website_resources_id = {$resourceId}";
        $result = $this->cms->db->query($sql);

        header('Content-type: application/json');
        echo json_encode($result);
    }

    /**
     * Adds a node
     * @param type $data
     */
    public function addNode($data)
    {
        // New, or being moved?
        
        // Being moved:
        // If rgt - lft = 1 , we can do this easily
        // If not, we need to ask whether to delete (or move) sub items
        // Moving sub items OR easy insert
        // Everything with a lft > this.lft, minus 1 lft and rgt. 
        // Everything a lft > this.rgt, minus 1 lft and rgt
        
        // If deleting, 
        // Everything with a lft > this.left, minus (this.rgt - this.lft) lft and rgt
        // Everything a lft > this.rgt, minus 1 lft and rgt
        // Every node with a lft > this.rgt && rgt < this.rgt DELETE
        
        // Moving an existing item ... tricky. Don't have the pseudo code
        
        // New item, depends on the parent it is being inserted into
    }

}

?>
