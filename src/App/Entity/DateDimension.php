<?php
declare(strict_types=1);
/**
 * /src/App/Entity/DateDimension.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Entity;

use App\Entity\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Class DateDimension
 *
 * @ORM\Table(
 *      name="date_dimension",
 *      indexes={
 *          @ORM\Index(name="date", columns={"date"}),
 *      }
 *  )
 * @ORM\Entity(
 *      repositoryClass="App\Repository\DateDimension"
 *  )
 *
 * @package App\Entity
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class DateDimension implements EntityInterface
{
    /**
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.id",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="id",
     *      type="guid",
     *      nullable=false,
     *  )
     * @ORM\Id()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.date",
     *  })
     * @JMS\Type("DateTime<'Y-m-d'>")
     *
     * @ORM\Column(
     *      name="date",
     *      type="date",
     *      nullable=false,
     *  )
     */
    private $date;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.year",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="year",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "A full numeric representation of a year, 4 digits",
     *      },
     *  )
     */
    private $year;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.month",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="month",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "Day of the month without leading zeros; 1 to 12",
     *      },
     *  )
     */
    private $month;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.day",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="day",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "Day of the month without leading zeros; 1 to 31",
     *      },
     *  )
     */
    private $day;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.quarter",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="quarter",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "Calendar quarter; 1, 2, 3 or 4",
     *      },
     *  )
     */
    private $quarter;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.weekNumber",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="week_number",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "ISO-8601 week number of year, weeks starting on Monday",
     *      },
     *  )
     */
    private $weekNumber;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.dayNumber",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="day_number_of_week",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "ISO-8601 numeric representation of the day of the week; 1 (for Monday) through 7 (for Sunday)",
     *      },
     *  )
     */
    private $dayNumberOfWeek;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.dayNumberOfYear",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="day_number_of_year",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "The day of the year (starting from 0); 0 through 365",
     *      },
     *  )
     */
    private $dayNumberOfYear;

    /**
     * @var boolean
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.leapYear",
     *  })
     * @JMS\Type("boolean")
     *
     * @ORM\Column(
     *      name="leap_year",
     *      type="boolean",
     *      nullable=false,
     *      options={
     *          "comment": "Whether it's a leap year",
     *      },
     *  )
     */
    private $leapYear;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.weekNumberingYear",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="week_numbering_year",
     *      type="integer",
     *      nullable=false,
     *      options={
     *          "comment": "ISO-8601 week-numbering year. This has the same value as year, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.",
     *      },
     *  )
     */
    private $weekNumberingYear;

    /**
     * @var integer
     *
     * @JMS\Groups({
     *      "Default",
     *      "DateDimension",
     *      "DateDimension.unixTime",
     *  })
     * @JMS\Type("integer")
     *
     * @ORM\Column(
     *      name="unix_time",
     *      type="bigint",
     *      nullable=false,
     *      options={
     *          "comment": "Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)",
     *      },
     *  )
     */
    private $unixTime;

    /**
     * DateDimension constructor.
     *
     * @param \DateTime $dateTime
     */
    public function __construct(\DateTime $dateTime = null)
    {
        $this->id = Uuid::uuid4()->toString();

        if ($dateTime !== null) {
            $this->setDate($dateTime);
            $this->setYear((int)$dateTime->format('Y'));
            $this->setMonth((int)$dateTime->format('n'));
            $this->setDay((int)$dateTime->format('j'));
            $this->setQuarter((int)\floor(((int)$dateTime->format('n') - 1) / 3) + 1);
            $this->setWeekNumber((int)$dateTime->format('W'));
            $this->setDayNumberOfWeek((int)$dateTime->format('N'));
            $this->setDayNumberOfYear((int)$dateTime->format('z'));
            $this->setLeapYear((bool)$dateTime->format('L'));
            $this->setWeekNumberingYear((int)$dateTime->format('o'));
            $this->setUnixTime((int)$dateTime->format('U'));
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return DateDimension
     */
    public function setDate(\DateTime $date): DateDimension
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return DateDimension
     */
    public function setYear(int $year): DateDimension
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * @param int $month
     *
     * @return DateDimension
     */
    public function setMonth(int $month): DateDimension
    {
        $this->month = $month;

        return $this;
    }

    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * @param int $day
     *
     * @return DateDimension
     */
    public function setDay(int $day): DateDimension
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuarter(): int
    {
        return $this->quarter;
    }

    /**
     * @param int $quarter
     *
     * @return DateDimension
     */
    public function setQuarter(int $quarter): DateDimension
    {
        $this->quarter = $quarter;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeekNumber(): int
    {
        return $this->weekNumber;
    }

    /**
     * @param int $weekNumber
     *
     * @return DateDimension
     */
    public function setWeekNumber(int $weekNumber): DateDimension
    {
        $this->weekNumber = $weekNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getDayNumberOfWeek(): int
    {
        return $this->dayNumberOfWeek;
    }

    /**
     * @param int $dayNumberOfWeek
     *
     * @return DateDimension
     */
    public function setDayNumberOfWeek(int $dayNumberOfWeek): DateDimension
    {
        $this->dayNumberOfWeek = $dayNumberOfWeek;

        return $this;
    }

    /**
     * @return int
     */
    public function getDayNumberOfYear(): int
    {
        return $this->dayNumberOfYear;
    }

    /**
     * @param int $dayNumberOfYear
     *
     * @return DateDimension
     */
    public function setDayNumberOfYear(int $dayNumberOfYear): DateDimension
    {
        $this->dayNumberOfYear = $dayNumberOfYear;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLeapYear(): bool
    {
        return $this->leapYear;
    }

    /**
     * @param boolean $leapYear
     *
     * @return DateDimension
     */
    public function setLeapYear(bool $leapYear): DateDimension
    {
        $this->leapYear = $leapYear;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeekNumberingYear(): int
    {
        return $this->weekNumberingYear;
    }

    /**
     * @param int $weekNumberingYear
     *
     * @return DateDimension
     */
    public function setWeekNumberingYear(int $weekNumberingYear): DateDimension
    {
        $this->weekNumberingYear = $weekNumberingYear;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnixTime(): int
    {
        return $this->unixTime;
    }

    /**
     * @param int $unixTime
     *
     * @return DateDimension
     */
    public function setUnixTime(int $unixTime): DateDimension
    {
        $this->unixTime = $unixTime;

        return $this;
    }
}
