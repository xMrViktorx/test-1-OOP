<?php
    interface Juicable {
        public function getVolume(): float;
    }

    // The Fruit class describes a fruit, which is determined by its color and volume.
    class Fruit implements Juicable {
        protected $color;
        protected $volume;

        public function __construct(string $color, float $volume) {
            $this->color = $color;
            $this->volume = $volume;
        }

        public function getVolume(): float {
            return $this->volume;
        }
    }

    // An apple is a fruit that can be rotten.
    class Apple extends Fruit {
        private $isRotten;

        public function __construct(string $color, float $volume, bool $isRotten) {
            parent::__construct($color, $volume);
            $this->isRotten = $isRotten;
        }

        public function isRotten(): bool {
            return $this->isRotten;
        }
    }

    // The juicer consists of two parts: a fruit container and a strainer.
    class Juicer {
        private $container;
        private $strainer;

        public function __construct(float $capacity) {
            $this->container = new FruitContainer($capacity);
            $this->strainer = new Strainer();
        }

        public function addFruit(Fruit $fruit): void {
            $this->container->addFruit($fruit);
        }

        public function squeeze(): void {
            $fruit = $this->container->removeFruit();
            if ($fruit) {
                $this->strainer->squeeze($fruit);
            } else {
                echo "No fruits to squeeze.\n";
            }
        }

        public function getTotalJuice(): float {
            return $this->strainer->getTotalJuice();
        }

        public function getRemainingCapacity(): float {
            return $this->container->getRemainingCapacity();
        }

        public function getFruitCount(): int {
            return $this->container->getFruitCount();
        }
    }

    // The fruit container has its capacity and can hold fruits.
    class FruitContainer {
        private $capacity;
        private $fruits = [];

        public function __construct(float $capacity) {
            $this->capacity = $capacity;
        }

        public function addFruit(Fruit $fruit): void {
            if ($this->getRemainingCapacity() < $fruit->getVolume()) {
                throw new Exception("Not enough capacity to add the fruit.");
            }
            $this->fruits[] = $fruit;
            echo "Added fruit: $fruit\n";
        }

        public function getRemainingCapacity(): float {
            $totalVolume = 0;
            foreach ($this->fruits as $fruit) {
                $totalVolume += $fruit->getVolume();
            }
            return $this->capacity - $totalVolume;
        }

        public function removeFruit(): ?Fruit {
            return array_shift($this->fruits);
        }

        public function getFruitCount(): int {
            return count($this->fruits);
        }
    }

    // The strainer is responsible for squeezing the fruits. With each squeeze, I can see how much juice is obtained.
    class Strainer {
        private $juiceVolume = 0;

        public function squeeze(Fruit $fruit): void {          
            if ($fruit instanceof Apple && $fruit->isRotten()) {
                throw new Exception("Cannot juice a rotten fruit.");
            }
   
            // Juicing one fruit yields an amount of juice equal to 50% of the fruit's volume.
            $juice = $fruit->getVolume() * 0.5;
            $this->juiceVolume += $juice;
            echo "Juiced fruit: $fruit. Juice obtained: " . number_format($juice, 2) . " liters.\n";
            
        }

        public function getTotalJuice(): float {
            return $this->juiceVolume;
        }
    }

    // Simulate the operation of a juicer with a volume of 20 liters:
    $juicer = new Juicer(20);

    // The juicer is programmed to perform 100 consecutive actions
    for ($action = 1; $action <= 100; $action++) {
        echo "Action $action:\n";

        // Every 9 squeezing actions, an additional apple is added
        if ($action % 9 === 0) {
            $volume = rand(1, 5) + rand(0, 99) / 100; // Apple volume between 1 and 5 liters
            $isRotten = rand(1, 100) <= 20; // 20% chance of being rotten
            $apple = new Apple("Red", $volume, $isRotten);

            try {
                $juicer->addFruit($apple);
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }

        // Squeeze the juicer
        try {
            $juicer->squeeze();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    // Display final juice volume
    echo "Total juice obtained: " . number_format($juicer->getTotalJuice(), 2) . " liters.\n";
?>
